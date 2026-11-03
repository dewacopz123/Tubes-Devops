<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PasienDashboardController;
use App\Services\ExpressApiService;
use Illuminate\Support\Facades\Http;

class PasienDashboardControllerTest extends TestCase
{
    public function test_index_renders_dashboard_view(): void
    {
        $this->withoutVite();

        Http::fake([
            '*/registrations/queue/today' => Http::response([
                'my' => ['no_antrian' => '005'],
                'stats' => ['total' => 10],
                'tanggal' => '2026-11-03',
            ], 200),
        ]);

        $controller = new PasienDashboardController();
        $api = new ExpressApiService();

        $view = $controller->index($api);
        $this->assertEquals('pages.pasien.dashboard', $view->name());
        $this->assertEquals('005', $view->getData()['current']['no_antrian']);
    }

    public function test_download_surat_rujukan_redirects_to_sas_url(): void
    {
        Http::fake([
            '*/registrations/5/surat-rujukan/download' => Http::response([
                'download_url' => 'https://azure.blob.core.windows.net/test.pdf?sas=123',
            ], 200),
        ]);

        $controller = new PasienDashboardController();
        $api = new ExpressApiService();

        $response = $controller->downloadSuratRujukan(5, $api);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('https://azure.blob.core.windows.net/test.pdf?sas=123', $response->getTargetUrl());
    }
}

