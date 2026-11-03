<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ExpressApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(ExpressApiService $api)
    {
        $profileResp = $api->authMe();
        $profile = $profileResp->ok() ? $profileResp->json() : null;

        return view('pages.pasien.pendaftaran', [
            'profile' => $profile,
        ]);
    }

    public function store(Request $request, ExpressApiService $api)
    {
        $validated = $request->validate([
            'tanggal_antrian' => ['required', 'date'],
            'jadwal_pemeriksaan_at' => ['required'],
            'surat_rujukan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ], [
            'tanggal_antrian.required' => 'Tanggal antrian wajib diisi',
            'jadwal_pemeriksaan_at.required' => 'Jadwal pemeriksaan wajib diisi',
            'surat_rujukan.required' => 'Surat rujukan wajib diupload',
            'surat_rujukan.file' => 'Surat rujukan harus berupa file',
            'surat_rujukan.mimes' => 'Surat rujukan harus berformat PDF, JPG, JPEG, atau PNG',
            'surat_rujukan.max' => 'Ukuran surat rujukan maksimal 5MB',
        ]);

        // hidden jadwal formatnya: YYYY-MM-DDTHH:mm
        try {
            $jadwal = Carbon::createFromFormat('Y-m-d\TH:i', $validated['jadwal_pemeriksaan_at'])
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['jadwal_pemeriksaan_at' => 'Format jadwal pemeriksaan tidak valid. Silakan pilih tanggal dan waktu pemeriksaan.']);
        }

        $payload = [
            'tanggal_antrian' => $validated['tanggal_antrian'],
            'jadwal_pemeriksaan_at' => $jadwal,
        ];

        $resp = $api->registrationCreate($payload, $request->file('surat_rujukan'));

        if ($resp->successful()) {
            $data = $resp->json();

            return redirect()
                ->route('pasien.pendaftaran.index')
                ->with('success', 'Pendaftaran berhasil! Nomor Antrian Anda: ' . ($data['no_antrian'] ?? '-') . ', Nomor Lab: ' . ($data['no_lab'] ?? '-'));
        }

        $msg = $resp->json('message') ?? 'Pendaftaran gagal. Silakan coba lagi atau hubungi petugas.';
        return back()->withInput()->with('error', $msg);
    }

    /**
     * List pendaftaran saya
     * GET /registrations/me
     */
    public function myRegistrations(Request $request, ExpressApiService $api)
    {
        $tanggal = $request->query('tanggal');
        
        $query = [];
        if ($tanggal) {
            $query['tanggal'] = $tanggal;
        }

        $resp = $api->registrationMe($query);

        if (!$resp->successful()) {
            return view('pages.pasien.list-pendaftaran', [
                'registrations' => [],
                'tanggal' => $tanggal,
                'errorMessage' => 'Gagal mengambil data pendaftaran. Silakan coba lagi.',
            ]);
        }

        $registrations = $resp->json();
        if (!is_array($registrations)) {
            $registrations = [];
        }

        return view('pages.pasien.list-pendaftaran', [
            'registrations' => $registrations,
            'tanggal' => $tanggal,
            'errorMessage' => null,
        ]);
    }

    /**
     * Antrian saya hari ini
     * GET /registrations/queue/today
     */
    public function myQueueToday(ExpressApiService $api)
    {
        $resp = $api->registrationQueueToday();

        if (!$resp->successful()) {
            return view('pages.pasien.antrian-saya', [
                'my' => null,
                'stats' => null,
                'tanggal' => null,
                'errorMessage' => 'Gagal mengambil data antrian. Silakan coba lagi.',
            ]);
        }

        $data = $resp->json();

        return view('pages.pasien.antrian-saya', [
            'my' => $data['my'] ?? null,
            'stats' => $data['stats'] ?? null,
            'tanggal' => $data['tanggal'] ?? null,
            'errorMessage' => null,
        ]);
    }

    /**
     * Download surat rujukan
     * GET /registrations/:id/surat-rujukan/download
     */
    public function downloadSuratRujukan(ExpressApiService $api, int $id)
    {
        $resp = $api->downloadSuratRujukan($id);

        if (!$resp->successful()) {
            $message = $resp->json('message') ?? 'Gagal mengunduh surat rujukan. File mungkin tidak ditemukan.';
            return back()->with('error', $message);
        }

        $data = $resp->json();
        $url = $data['url'] ?? null;
        $filename = $data['filename'] ?? 'surat-rujukan.pdf';

        if (!$url) {
            return back()->with('error', 'URL download tidak tersedia.');
        }

        // Direct download menggunakan file_get_contents dan response()->streamDownload
        try {
            $fileContents = file_get_contents($url);
            
            if ($fileContents === false) {
                return back()->with('error', 'Gagal mengunduh file dari server.');
            }

            $contentType = $data['content_type'] ?? 'application/pdf';

            return response()->streamDownload(function () use ($fileContents) {
                echo $fileContents;
            }, $filename, [
                'Content-Type' => $contentType,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }
}
