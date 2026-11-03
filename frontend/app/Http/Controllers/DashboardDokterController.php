<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class DashboardDokterController extends Controller
{
    public function index(Request $request, ExpressApiService $api)
    {
        $queues = [];
        $tanggal = null;

        /**
         * Ambil data antrian hari ini
         */
        $response = $api->queueToday();

        if ($response->successful()) {
            $payload = $response->json();
            $tanggal = $payload['tanggal'] ?? null;
            $queues = $payload['data'] ?? [];
        }

        /**
         * Normalisasi status
         */
        $normalize = fn ($s) => strtoupper(trim((string) $s));

        /**
         * Hitung statistik
         */
        $stats = [
            'total' => count($queues),
            'menunggu' => 0,
            'dilayani' => 0,
        ];

        foreach ($queues as $q) {
            $status = $normalize($q['status'] ?? '');

            if (in_array($status, ['MENUNGGU', 'PENDING'])) {
                $stats['menunggu']++;
            }

            if (in_array($status, ['DILAYANI', 'PROSES'])) {
                $stats['dilayani']++;
            }
        }

        /**
         * Cari antrian aktif (sedang dilayani)
         */
        $current = null;
        foreach ($queues as $q) {
            $status = $normalize($q['status'] ?? '');
            if (in_array($status, ['DILAYANI', 'PROSES'])) {
                $current = $q;
                break;
            }
        }

        return view('pages.dokter.dashboard', [
            'queues' => $queues,
            'stats' => $stats,
            'current' => $current,
            'tanggal' => $tanggal,
        ]);
    }
}
