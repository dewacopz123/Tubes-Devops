<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Traits\HandlesApiResponses;
use App\Services\ExpressApiService;
use Illuminate\Http\Client\Response as ClientResponse;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class DummyControllerWithTrait
{
    use HandlesApiResponses;

    public function testHandle(ClientResponse $response, ExpressApiService $api, string $msg = '', ?string $redirect = null)
    {
        return $this->handleApiResponse($response, $api, $msg, $redirect);
    }

    public function testErrorView(ClientResponse $response, ExpressApiService $api): ?string
    {
        return $this->getApiErrorForView($response, $api);
    }
}

class HandlesApiResponsesTest extends TestCase
{
    public function test_get_api_error_for_view_returns_null_on_success(): void
    {
        $dummy = new DummyControllerWithTrait();
        $api = new ExpressApiService();
        $guzzle = new GuzzleResponse(200, [], json_encode(['data' => 'success']));
        $response = new ClientResponse($guzzle);

        $this->assertNull($dummy->testErrorView($response, $api));
    }

    public function test_get_api_error_for_view_returns_message_on_failure(): void
    {
        $dummy = new DummyControllerWithTrait();
        $api = new ExpressApiService();
        $guzzle = new GuzzleResponse(404, [], json_encode(['message' => 'Not Found']));
        $response = new ClientResponse($guzzle);

        $this->assertEquals('Not Found', $dummy->testErrorView($response, $api));
    }

    public function test_get_api_error_for_view_handles_401(): void
    {
        $dummy = new DummyControllerWithTrait();
        $api = new ExpressApiService();
        $guzzle = new GuzzleResponse(401, [], json_encode(['message' => 'Unauthorized']));
        $response = new ClientResponse($guzzle);

        $this->assertStringContainsString('Sesi Anda telah berakhir', $dummy->testErrorView($response, $api));
    }
}
