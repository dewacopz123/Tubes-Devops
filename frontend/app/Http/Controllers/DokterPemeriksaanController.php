<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;

class DokterPemeriksaanController extends Controller
{
    public function index(int $pasienId, ExpressApiService $api)
    {
        $baseUrl = rtrim(config('services.express_api.url'), '/');

        // Ambil data pasien
        $pasienName = null;
        try {
            $p = $api->pasienDetail($pasienId);
            if ($p->successful()) {
                $pasienName = data_get($p->json(), 'data.nama')
                    ?? data_get($p->json(), 'nama');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Ambil semua pemeriksaan pasien (tanpa files)
        $res = $api->examsByPatient($pasienId);

        if (!$res->successful()) {
            return view('pages.dokter.pasien_pemeriksaan', [
                'pasienId' => $pasienId,
                'pasienName' => $pasienName,
                'exams' => [],
                'errorMessage' => $res->json('message') ?? 'Gagal mengambil data pemeriksaan pasien.',
            ]);
        }

        $json = $res->json();
        $exams = data_get($json, 'data', []);
        if (!is_array($exams)) $exams = [];

        // Filter dokter: hanya HASIL_TERSEDIA
        $exams = array_values(array_filter($exams, function ($e) {
            return ($e['status_hasil'] ?? null) === 'HASIL_TERSEDIA';
        }));

        // ambil files dari endpoint detail /exams/:id
        // bentuk download_url dari file_path
        $enriched = [];
        foreach ($exams as $e) {
            $examId = $e['pemeriksaan_id'] ?? $e['id'] ?? null;
            if (!$examId) {
                $e['files'] = [];
                $e['download_url'] = null;
                $enriched[] = $e;
                continue;
            }

            try {
                $detail = $api->detail((int) $examId);

                $files = data_get($detail, 'files', []);
                if (!is_array($files)) $files = [];

                // Ambil file terbaru
                $latest = $files[0] ?? null;

                $downloadUrl = null;
                if ($latest && !empty($latest['file_path'])) {
                    // file_path disimpan seperti "/uploads/namafile.ext"
                    $downloadUrl = $baseUrl . $latest['file_path'];
                }

                $e['files'] = $files;
                $e['download_url'] = $downloadUrl;
            } catch (\Throwable $err) {
                $e['files'] = [];
                $e['download_url'] = null;
            }

            $enriched[] = $e;
        }

        return view('pages.dokter.pasien_pemeriksaan', [
            'pasienId' => $pasienId,
            'pasienName' => $pasienName,
            'exams' => $enriched,
            'errorMessage' => null,
        ]);
    }
}
