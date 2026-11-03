<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    private function kategoriList(): array
    {
        return [
            ['id' => 1, 'nama' => 'Patologi'],
            ['id' => 2, 'nama' => 'Anatomi'],
            ['id' => 3, 'nama' => 'Mikrobiologi'],
        ];
    }

    public function index(Request $request, ExpressApiService $api)
    {
        $q = $request->query('q');
        $statusHasil = $request->query('status_hasil');

        $page  = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 20);

        $res = $api->examsList(array_filter([
            'q' => $q,
            'status_hasil' => $statusHasil,
            'page' => $page,
            'limit' => $limit,
        ], fn($v) => $v !== null && $v !== ''));

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengambil data pemeriksaan. Silakan coba lagi.';
            return view('pages.admin.pemeriksaan', [
                'exams' => [],
                'meta' => ['page' => $page, 'limit' => $limit, 'total' => 0, 'total_pages' => 1],
                'q' => $q,
                'statusHasil' => $statusHasil,
                'kategoriList' => $this->kategoriList(),
                'errorMessage' => $msg,
            ]);
        }

        $json = $res->json();

        return view('pages.admin.pemeriksaan', [
            'exams' => data_get($json, 'data', []),
            'meta' => data_get($json, 'meta', ['page' => $page, 'limit' => $limit]),
            'q' => $q,
            'statusHasil' => $statusHasil,
            'kategoriList' => $this->kategoriList(),
            'errorMessage' => null,
        ]);
    }

    /**
     * Show detail pemeriksaan
     * GET /exams/:id
     */
    public function show(ExpressApiService $api, int $id)
    {
        $res = $api->examDetail($id);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Pemeriksaan tidak ditemukan.';
            return back()->with('error', $msg);
        }

        $exam = $res->json();

        return view('pages.admin.detail-pemeriksaan', [
            'exam' => $exam,
            'kategoriList' => $this->kategoriList(),
        ]);
    }

    public function store(Request $request, ExpressApiService $api)
    {
        $validated = $request->validate([
            'pendaftaran_id' => ['required', 'integer', 'min:1'],
            'kategori_id' => ['required', 'integer', 'in:1,2,3'],
            'catatan' => ['nullable', 'string'],
        ], [
            'pendaftaran_id.required' => 'ID pendaftaran wajib diisi',
            'kategori_id.required' => 'Kategori pemeriksaan wajib dipilih',
            'kategori_id.in' => 'Kategori pemeriksaan tidak valid',
        ]);

        $res = $api->createExam($validated);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal membuat pemeriksaan. Silakan coba lagi.';
            return back()->withInput()->with('error', $msg);
        }

        return back()->with('success', 'Pemeriksaan berhasil dibuat.');
    }

    public function update(Request $request, ExpressApiService $api, int $id)
    {
        // Hapus TIDAK_TERSEDIA dari validation - hanya allow MENUNGGU_HASIL dan HASIL_TERSEDIA
        $validated = $request->validate([
            'status_validasi' => ['required', 'string', 'in:DRAFT,TERVALIDASI'],
            'status_hasil' => ['required', 'string', 'in:MENUNGGU_HASIL,HASIL_TERSEDIA'],
            'catatan' => ['nullable', 'string'],
        ], [
            'status_validasi.required' => 'Status validasi wajib dipilih',
            'status_validasi.in' => 'Status validasi tidak valid',
            'status_hasil.required' => 'Status hasil wajib dipilih',
            'status_hasil.in' => 'Status hasil tidak valid. Pilih MENUNGGU_HASIL atau HASIL_TERSEDIA',
        ]);

        $payload = [
            'status_validasi' => $validated['status_validasi'],
            'status_hasil' => $validated['status_hasil'],
            'catatan' => $validated['catatan'] ?? null,
        ];

        $res = $api->patchExam($id, $payload);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal memperbarui pemeriksaan. Silakan coba lagi.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'Pemeriksaan berhasil diperbarui.');
    }

    public function uploadFile(Request $request, ExpressApiService $api, int $id)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // max 5MB
        ], [
            'file.required' => 'File hasil pemeriksaan wajib diupload',
            'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        $res = $api->uploadExamFile($id, $validated['file']);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengupload file pemeriksaan. Silakan coba lagi.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'File hasil pemeriksaan berhasil diupload.');
    }

    /**
     * Replace exam file
     * PATCH /exams/:id/files/:fileId
     */
    public function replaceFile(Request $request, ExpressApiService $api, int $id, int $fileId)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // max 5MB
        ], [
            'file.required' => 'File pengganti wajib diupload',
            'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        $res = $api->replaceExamFile($id, $fileId, $validated['file']);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengganti file pemeriksaan. Silakan coba lagi.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'File hasil pemeriksaan berhasil diganti.');
    }

    /**
     * Download exam file
     * GET /exams/:id/files/:fileId/download
     */
    public function downloadFile(ExpressApiService $api, int $id, int $fileId)
    {
        $res = $api->downloadExamFile($id, $fileId);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal mengunduh file hasil pemeriksaan. File mungkin tidak ditemukan.';
            return back()->with('error', $msg);
        }

        $data = $res->json();
        $url = $data['url'] ?? null;
        $filename = $data['filename'] ?? 'hasil-pemeriksaan.pdf';

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

    /**
     * Delete exam
     * DELETE /exams/:id
     */
    public function destroy(ExpressApiService $api, int $id)
    {
        $res = $api->deleteExam($id);

        if (!$res->successful()) {
            $msg = $res->json('message') ?? 'Gagal menghapus pemeriksaan. Silakan coba lagi.';
            return back()->with('error', $msg);
        }

        $data = $res->json();
        $deletedFiles = $data['deletedFiles'] ?? 0;

        return redirect()
            ->route('petugas.pemeriksaan.index')
            ->with('success', "Pemeriksaan berhasil dihapus beserta {$deletedFiles} file terkait.");
    }
}
