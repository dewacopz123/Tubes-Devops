<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class AntrianPasienController extends Controller
{
    public function index(Request $request, ExpressApiService $apiService)
    {
        $tanggal = null;
        $queues = [];

        $response = $apiService->queueToday();

        if ($response->successful()) {
            $data = $response->json();
            $tanggal = $data['tanggal'] ?? null;
            $queues = $data['data'] ?? [];
        }

        $norm = function ($s) {
            $s = strtoupper(trim((string) $s));
            $map = [
                'PENDING' => 'MENUNGGU',
                'PROSES'  => 'DILAYANI',
                'DONE'    => 'SELESAI',
                'CANCEL'  => 'DIBATALKAN',
                'CANCELED'=> 'DIBATALKAN',
                'BATAL'   => 'DIBATALKAN',
            ];
            return $map[$s] ?? $s;
        };

        $stats = [
            'total' => count($queues),
            'menunggu' => 0,
            'dilayani' => 0,
            'selesai' => 0,
            'dibatalkan' => 0,
        ];

        foreach ($queues as $q) {
            $st = $norm($q['status'] ?? '');
            if ($st === 'MENUNGGU') $stats['menunggu']++;
            elseif ($st === 'DILAYANI') $stats['dilayani']++;
            elseif ($st === 'SELESAI') $stats['selesai']++;
            elseif ($st === 'DIBATALKAN') $stats['dibatalkan']++;
        }

        $current = null;
        $dilayaniList = array_values(array_filter($queues, function ($q) use ($norm) {
            return $norm($q['status'] ?? '') === 'DILAYANI';
        }));

        if (count($dilayaniList) > 0) {
            usort($dilayaniList, function ($a, $b) {
                return ((int)($a['no_antrian'] ?? 0)) <=> ((int)($b['no_antrian'] ?? 0));
            });
            $current = $dilayaniList[0];
        }

        $pasienId = (int) session('pasien_id');
        if ($pasienId <= 0) {
            $profileResp = $apiService->authMe();
            if ($profileResp->successful()) {
                $pasienId = $profileResp->json('profil.id') ?? 0;
                if ($pasienId > 0) {
                    session(['pasien_id' => $pasienId]);
                }
            }
        }

        return view('pages.pasien.antrian', [
            'tanggal' => $tanggal,
            'queues' => $queues,
            'stats' => $stats,
            'current' => $current,
            'myPasienId' => $pasienId,
        ]);
    }
}
