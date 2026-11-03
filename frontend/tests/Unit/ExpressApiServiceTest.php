<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ExpressApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response as ClientResponse;

class ExpressApiServiceTest extends TestCase
{
    private ExpressApiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.express_api.url' => 'http://localhost:5000/api']);
        $this->service = new ExpressApiService();
    }

    public function test_user_friendly_error_messages(): void
    {
        $g401 = new ClientResponse(new GuzzleResponse(401, [], json_encode(['message' => 'Unauthorized'])));
        $this->assertStringContainsString('Sesi Anda telah berakhir', $this->service->getUserFriendlyError($g401));

        $g403 = new ClientResponse(new GuzzleResponse(403, [], json_encode(['message' => 'Forbidden'])));
        $this->assertStringContainsString('tidak memiliki akses', $this->service->getUserFriendlyError($g403));

        $g404 = new ClientResponse(new GuzzleResponse(404, [], json_encode(['message' => 'Item not found'])));
        $this->assertEquals('Item not found', $this->service->getUserFriendlyError($g404));

        $g429 = new ClientResponse(new GuzzleResponse(429, [], json_encode(['message' => 'Too many requests'])));
        $this->assertStringContainsString('Terlalu banyak percobaan', $this->service->getUserFriendlyError($g429));

        $g500 = new ClientResponse(new GuzzleResponse(500, [], json_encode(['message' => 'Internal server error'])));
        $this->assertStringContainsString('kesalahan pada server', $this->service->getUserFriendlyError($g500));

        $g503 = new ClientResponse(new GuzzleResponse(503, [], json_encode(['message' => 'Service unavailable'])));
        $this->assertStringContainsString('tidak tersedia', $this->service->getUserFriendlyError($g503));
    }

    public function test_is_session_expired_and_rate_limited(): void
    {
        $res401 = new ClientResponse(new GuzzleResponse(401));
        $res429 = new ClientResponse(new GuzzleResponse(429));
        $res200 = new ClientResponse(new GuzzleResponse(200));

        $this->assertTrue($this->service->isSessionExpired($res401));
        $this->assertFalse($this->service->isSessionExpired($res200));

        $this->assertTrue($this->service->isRateLimited($res429));
        $this->assertFalse($this->service->isRateLimited($res200));
    }

    public function test_auth_endpoints(): void
    {
        Http::fake([
            '*/auth/login' => Http::response(['token' => 'jwt-test-token'], 200),
            '*/auth/register' => Http::response(['success' => true], 201),
            '*/auth/register-dokter' => Http::response(['success' => true], 201),
            '*/auth/register-petugas' => Http::response(['success' => true], 201),
            '*/auth/me' => Http::response(['id' => 1, 'username' => 'testuser'], 200),
        ]);

        $login = $this->service->login('admin', 'secret123');
        $this->assertEquals(200, $login->status());
        $this->assertEquals('jwt-test-token', $login->json('token'));

        $regPasien = $this->service->registerPasien(['nama' => 'Budi']);
        $this->assertEquals(201, $regPasien->status());

        $regDokter = $this->service->registerDokter(['nama' => 'Dr. Andi']);
        $this->assertEquals(201, $regDokter->status());

        $regPetugas = $this->service->registerPetugas(['nama' => 'Siti']);
        $this->assertEquals(201, $regPetugas->status());

        $me = $this->service->authMe();
        $this->assertEquals(200, $me->status());
    }

    public function test_registration_endpoints(): void
    {
        Http::fake([
            '*/registrations/' => Http::response(['id' => 10], 201),
            '*/registrations/me*' => Http::response([['id' => 10]], 200),
            '*/registrations/queue/today' => Http::response(['queue' => 5], 200),
            '*/registrations/10/surat-rujukan/download' => Http::response('pdfcontent', 200),
        ]);

        $create = $this->service->registrationCreate(['keluhan' => 'Demam'], null);
        $this->assertEquals(201, $create->status());

        $me = $this->service->registrationMe();
        $this->assertEquals(200, $me->status());

        $queue = $this->service->registrationQueueToday();
        $this->assertEquals(200, $queue->status());

        $download = $this->service->downloadSuratRujukan(10);
        $this->assertEquals(200, $download->status());
    }

    public function test_patient_and_queue_endpoints(): void
    {
        Http::fake([
            '*/patients/' => Http::response([['id' => 1]], 200),
            '*/patients/1' => Http::response(['id' => 1], 200),
            '*/patients/search' => Http::response([['id' => 1]], 200),
            '*/queue/today*' => Http::response([], 200),
            '*/queue/stats' => Http::response(['total' => 10], 200),
            '*/queue/1/call' => Http::response(['called' => true], 200),
            '*/queue/1/next' => Http::response(['next' => true], 200),
            '*/queue/1/cancel' => Http::response(['cancelled' => true], 200),
        ]);

        $this->assertEquals(200, $this->service->pasien()->status());
        $this->assertEquals(200, $this->service->pasienDetail(1)->status());
        $this->assertEquals(200, $this->service->patientSearch(['nama' => 'Test'])->status());
        $this->assertEquals(200, $this->service->queueToday()->status());
        $this->assertEquals(200, $this->service->queueStats()->status());
        $this->assertEquals(200, $this->service->queueCall(1)->status());
        $this->assertEquals(200, $this->service->queueNext(1)->status());
        $this->assertEquals(200, $this->service->queueCancel(1, 'Batal')->status());
    }

    public function test_exam_and_device_endpoints(): void
    {
        Http::fake([
            '*/exams/all*' => Http::response([], 200),
            '*/exams/patients/1' => Http::response([], 200),
            '*/exams/5' => Http::response(['id' => 5], 200),
            '*/exams/' => Http::response(['id' => 5], 201),
            '*/devices/token' => Http::response(['success' => true], 200),
            '*/audit-logs*' => Http::response([], 200),
        ]);

        $this->assertEquals(200, $this->service->examsList()->status());
        $this->assertEquals(200, $this->service->examsByPatient(1)->status());
        $this->assertEquals(200, $this->service->examDetail(5)->status());
        $this->assertEquals(201, $this->service->createExam(['pasien_id' => 1])->status());
        $this->assertEquals(200, $this->service->saveDeviceToken('token123')->status());
        $this->assertEquals(200, $this->service->auditLogs()->status());
    }
}
