<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request, ExpressApiService $api)
    {
        $kategori = strtolower((string) $request->query('kategori', 'semua'));
        $q = trim((string) $request->query('q', ''));

        // Ambil pasien_id dari session (sudah disimpan saat login/register di AuthController)
        $pasienId = (int) session('pasien_id');

        // Fallback: jika pasien_id tidak ada di session, coba ambil dari /auth/me
        if ($pasienId <= 0 && session()->has('api_token')) {
            $me = $api->authMe();

            if ($me->successful()) {
                $profil = $me->json('profil') ?? [];
                $pasienId = (int) ($profil['id'] ?? 0);

                if ($pasienId > 0) {
                    session(['pasien_id' => $pasienId]);
                }
            }
        }

        // Jika masih tidak ada pasien_id, tampilkan error
        if ($pasienId <= 0) {
            return view('pages.pasien.riwayat', [
                'items' => [],
                'kategori' => $kategori,
                'q' => $q,
                'errorMessage' => 'Tidak dapat mengambil data profil Anda. Silakan logout dan login kembali.',
            ]);
        }

        // Call API: GET /exams/patients/:pasienId
        $res = $api->examsByPatient($pasienId);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Gagal mengambil riwayat pemeriksaan. Silakan coba lagi.';
            return view('pages.pasien.riwayat', [
                'items' => [],
                'kategori' => $kategori,
                'q' => $q,
                'errorMessage' => $message,
            ]);
        }

        $items = $res->json('data') ?? [];

        // Filter by kategori (manual filtering karena API tidak support)
        if ($kategori !== 'semua') {
            $items = array_values(array_filter($items, function ($it) use ($kategori) {
                return strtolower((string) ($it['kategori_nama'] ?? '')) === $kategori;
            }));
        }

        // Filter by nomor lab (manual filtering)
        if ($q !== '') {
            $items = array_values(array_filter($items, function ($it) use ($q) {
                $noLab = (string) ($it['no_lab'] ?? '');
                return str_contains($noLab, $q);
            }));
        }

        return view('pages.pasien.riwayat', [
            'items' => $items,
            'kategori' => $kategori,
            'q' => $q,
            'errorMessage' => null,
        ]);
    }
}
