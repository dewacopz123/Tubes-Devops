<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HasilPemeriksaanController extends Controller
{
    protected ExpressApiService $api;

    public function __construct(ExpressApiService $api)
    {
        $this->api = $api;
    }

    /**
     * List hasil pemeriksaan pasien
     * GET /exams/patients/:pasienId
     */
    public function index(Request $request): View
    {
        // Ambil pasien_id dari session (sudah disimpan oleh AuthController)
        $pasienId = (int) session('pasien_id');

        // Fallback: jika tidak ada di session, coba ambil dari /auth/me
        if ($pasienId <= 0) {
            $profileResp = $this->api->authMe();
            
            if ($profileResp->successful()) {
                $pasienId = $profileResp->json('profil.id') ?? 0;
                
                if ($pasienId > 0) {
                    session(['pasien_id' => $pasienId]);
                }
            }
        }

        if ($pasienId <= 0) {
            return view('pages.pasien.hasilPemeriksaan', [
                'items' => [],
                'kategori' => 'semua',
                'q' => '',
                'errorMessage' => 'Tidak dapat mengambil data profil Anda. Silakan logout dan login kembali.',
            ]);
        }

        // Ambil query parameters
        $kategori = strtolower($request->get('kategori', 'semua'));
        $q = trim($request->get('q', ''));

        // Call API: GET /exams/patients/:pasienId
        $res = $this->api->examsByPatient($pasienId);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Gagal mengambil data hasil pemeriksaan. Silakan coba lagi.';
            return view('pages.pasien.hasilPemeriksaan', [
                'items' => [],
                'kategori' => $kategori,
                'q' => $q,
                'errorMessage' => $message,
            ]);
        }

        $items = $res->json('data') ?? [];
        if (!is_array($items)) {
            $items = [];
        }

        /**
         * Filter: hanya tampilkan status_hasil = 'HASIL_TERSEDIA'
         */
        $items = array_filter($items, function ($item) {
            $status = $item['status_hasil'] ?? $item['statusHasil'] ?? null;
            $status = strtoupper(trim((string) $status));
            return $status === 'HASIL_TERSEDIA';
        });

        // Filter by kategori
        if ($kategori !== 'semua') {
            $items = array_filter($items, function ($item) use ($kategori) {
                return strtolower(trim((string)($item['kategori_nama'] ?? ''))) === $kategori;
            });
        }

        // Filter by nomor lab
        if ($q !== '') {
            $qLower = strtolower($q);
            $items = array_filter($items, function ($item) use ($qLower) {
                return str_contains(
                    strtolower((string)($item['no_lab'] ?? '')),
                    $qLower
                );
            });
        }

        return view('pages.pasien.hasilPemeriksaan', [
            'items' => array_values($items),
            'kategori' => $kategori,
            'q' => $q,
            'errorMessage' => null,
        ]);
    }

    /**
     * Detail hasil pemeriksaan
     * GET /exams/:id
     */
    public function show($id): View
    {
        $res = $this->api->examDetail($id);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Detail hasil pemeriksaan tidak ditemukan.';
            
            return view('pages.pasien.detailHasilPemeriksaan', [
                'data' => null,
                'errorMessage' => $message,
            ]);
        }

        $data = $res->json();

        return view('pages.pasien.detailHasilPemeriksaan', [
            'data' => $data,
            'examId' => $id, // Pass exam ID from route parameter
            'errorMessage' => null,
        ]);
    }

    /**
     * Download exam file
     * GET /exams/:examId/files/:fileId/download
     */
    public function downloadFile($examId, $fileId)
    {
        $res = $this->api->downloadExamFile($examId, $fileId);

        if (!$res->successful()) {
            return back()->with('error', 'File tidak ditemukan atau tidak dapat diakses.');
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
