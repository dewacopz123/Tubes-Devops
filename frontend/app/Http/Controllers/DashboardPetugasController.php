<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class DashboardPetugasController extends Controller
{
    public function index(Request $request, ExpressApiService $apiService)
    {
        // Default
        $queueStats = [
            'total' => 0,
            'menunggu' => 0,
            'selesai' => 0,
            'dibatalkan' => 0,
        ];

        $page  = (int) $request->query('page', 1);
        $limit = 20;

        $auditLogs = [];
        $auditMeta = [
            'page' => $page,
            'hasNext' => false,
            'hasPrev' => false,
        ];

        $response = $apiService->auditLogs([
            'page' => $page,
            'limit' => $limit,
        ]);

        if ($response->successful()) {
            $json = $response->json();

            $auditLogs = $json['data'] ?? [];
            $auditMeta = $json['meta'] ?? $auditMeta;
        }

        $response = $apiService->queueStats();

        if ($response->successful()) {
            $json = $response->json();

            if (isset($json['stats'])) {
                $stats = $json['stats'];

                $queueStats = [
                    'total' => (int) ($stats['total'] ?? 0),
                    'menunggu' => (int) ($stats['menunggu'] ?? 0),
                    'selesai' => (int) ($stats['selesai'] ?? 0),
                    'dibatalkan' => (int) ($stats['dibatalkan'] ?? 0),
                ];
            }
        }

        return view('pages.admin.dashboard', [
            'queueStats' => $queueStats,
            'auditLogs' => $auditLogs,
            'auditMeta' => $auditMeta,
        ]);
    }
}
