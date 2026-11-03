<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class PasienPemeriksaanController extends Controller
{
    public function index(int $id, Request $request, ExpressApiService $api)
    {
        // ambil info pasien
        $pasienName = null;
        try {
            $p = $api->pasienDetail($id);
            if ($p->successful()) {
                $pasienName = data_get($p->json(), 'data.nama')
                    ?? data_get($p->json(), 'nama')
                    ?? data_get($p->json(), 'data.name')
                    ?? data_get($p->json(), 'name');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Fetch patient's registrations using the new endpoint
        $registrations = [];
        try {
            $regRes = $api->get("/patients/{$id}/registrations");
            if ($regRes->successful()) {
                $registrations = data_get($regRes->json(), 'data', []);
                if (!is_array($registrations)) $registrations = [];
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $res = $api->examsByPatient($id);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengambil hasil pemeriksaan pasien.';
            return view('pages.admin.pasien_pemeriksaan', [
                'pasienId' => $id,
                'pasienName' => $pasienName,
                'exams' => [],
                'registrations' => $registrations,
                'errorMessage' => $msg,
            ]);
        }

        $json = $res->json();
        $exams = data_get($json, 'data');
        if (!is_array($exams)) $exams = [];

        return view('pages.admin.pasien_pemeriksaan', [
            'pasienId' => $id,
            'pasienName' => $pasienName,
            'exams' => $exams,
            'registrations' => $registrations,
            'errorMessage' => null,
        ]);
    }

    public function updateExam(int $examId, Request $request, ExpressApiService $api)
    {
        $validated = $request->validate([
            'status_validasi' => ['required', 'string', 'in:DRAFT,TERVALIDASI'],
            'status_hasil' => ['required', 'string', 'in:MENUNGGU_HASIL,HASIL_TERSEDIA,TIDAK_TERSEDIA'],
            'catatan' => ['nullable', 'string'],
        ]);

        $res = $api->patchExam($examId, [
            'status_validasi' => $validated['status_validasi'],
            'status_hasil' => $validated['status_hasil'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal memperbarui pemeriksaan.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'Pemeriksaan berhasil diperbarui.');
    }

    public function uploadExamFile(int $examId, Request $request, ExpressApiService $api)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $res = $api->uploadExamFile($examId, $validated['file']);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal upload file pemeriksaan.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'File pemeriksaan berhasil diupload.');
    }

    public function downloadSuratRujukan(int $id, ExpressApiService $api)
    {
        $res = $api->get("/registrations/{$id}/surat-rujukan/download");

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengambil surat rujukan.';
            return back()->with('error', $msg);
        }

        $url = data_get($res->json(), 'url');
        
        if (!$url) {
            return back()->with('error', 'URL download tidak ditemukan.');
        }

        // Redirect to the SAS URL for download
        return redirect($url);
    }
}
