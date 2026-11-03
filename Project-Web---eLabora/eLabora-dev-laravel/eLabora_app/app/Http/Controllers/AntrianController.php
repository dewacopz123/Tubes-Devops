<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class AntrianController extends Controller
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

        return view('pages.admin.antrian', [
            'tanggal' => $tanggal,
            'queues' => $queues,
        ]);
    }

    public function call(Request $request, ExpressApiService $apiService, int $id)
    {
        $response = $apiService->queueCall($id);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal memanggil antrian.');
        }

        return back()->with('success', 'Antrian berhasil dipanggil.');
    }

    public function next(Request $request, ExpressApiService $apiService, int $id)
    {
        $response = $apiService->queueNext($id);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal ke antrian berikutnya.');
        }

        $data = $response->json();
        $finished = $data['finished'] ?? null;

        return back()->with(
            'success',
            'Berhasil lanjut ke antrian berikutnya. Selesai: ' . ($finished ?? '-')
        );
    }

    public function cancel(Request $request, ExpressApiService $apiService, int $id)
    {
        $response = $apiService->queueCancel($id);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal membatalkan antrian.');
        }

        return back()->with('success', 'Antrian berhasil dibatalkan.');
    }
}
