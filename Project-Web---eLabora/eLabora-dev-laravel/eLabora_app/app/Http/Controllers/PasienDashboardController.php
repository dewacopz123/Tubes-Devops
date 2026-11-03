<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ExpressApiService;

class PasienDashboardController extends Controller
{
    public function index(ExpressApiService $api)
    {
        $queues = [];
        $current = null;
        $stats = null;
        $tanggal = null;

        // Gunakan endpoint khusus pasien: GET /registrations/queue/today
        // Endpoint ini return: { my: {...}, stats: {...}, tanggal: "..." }
        $res = $api->registrationQueueToday();
        
        if ($res->successful()) {
            $json = $res->json();

            // Data antrian pasien sendiri
            $current = $json['my'] ?? null;
            
            // Statistik antrian hari ini
            $stats = $json['stats'] ?? null;
            
            // Tanggal
            $tanggal = $json['tanggal'] ?? null;

            // Untuk backward compatibility dengan view, ambil semua antrian juga
            // Jika view memerlukan list queues, kita bisa call endpoint lain
            // Tapi untuk dashboard pasien, yang penting adalah 'my' dan 'stats'
        } else {
            // Handle error dengan graceful
            $errorMessage = $res->json('message') ?? null;
        }

        return view('pages.pasien.dashboard', [
            'current' => $current,
            'stats' => $stats,
            'tanggal' => $tanggal,
            'queues' => $queues, // Untuk backward compatibility
            'errorMessage' => $errorMessage ?? null,
        ]);
    }

    /**
     * Download surat rujukan
     * GET /registrations/:id/surat-rujukan/download
     */
    public function downloadSuratRujukan($id, ExpressApiService $api)
    {
        $res = $api->downloadSuratRujukan($id);

        if (!$res->successful()) {
            return back()->with('error', 'Surat rujukan tidak ditemukan atau tidak dapat diakses.');
        }

        // API returns { download_url: "https://..." }
        $downloadUrl = $res->json('download_url');

        if (empty($downloadUrl)) {
            return back()->with('error', 'URL download tidak tersedia.');
        }

        // Redirect to the SAS URL
        return redirect($downloadUrl);
    }
}