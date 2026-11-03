<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasienController extends Controller
{
    public function index(Request $request, ExpressApiService $api)
    {
        $q = trim((string) $request->query('q'));

        // Ambil semua pasien dari API
        $res = $api->pasien();

        if (!$res->successful()) {
            return view('pages.dokter.pasien', [
                'patients' => [],
                'q' => $q,
                'errorMessage' => 'Gagal mengambil data pasien.',
            ]);
        }

        $json = $res->json();

        $patients = data_get($json, 'items', []);
        if (!is_array($patients)) {
            $patients = [];
        }

        if ($q !== '') {
            $qLower = Str::lower($q);

            $patients = array_values(array_filter($patients, function ($p) use ($qLower) {
                return
                    Str::contains(Str::lower($p['nama'] ?? ''), $qLower) ||
                    Str::contains(Str::lower($p['nik'] ?? ''), $qLower) ||
                    Str::contains(Str::lower($p['no_telepon'] ?? ''), $qLower) ||
                    Str::contains(Str::lower($p['username'] ?? ''), $qLower) ||
                    Str::contains(Str::lower($p['email'] ?? ''), $qLower);
            }));
        }

        return view('pages.dokter.pasien', [
            'patients' => $patients,
            'q' => $q,
            'errorMessage' => null,
        ]);
    }
}
