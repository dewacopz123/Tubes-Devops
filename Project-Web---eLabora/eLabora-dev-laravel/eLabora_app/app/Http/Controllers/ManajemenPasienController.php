<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class ManajemenPasienController extends Controller
{
    /**
     * List semua pasien dengan simple search
     * GET /patients/
     */
    public function index(Request $request, ExpressApiService $api)
    {
        $search = trim((string) $request->query('search'));
        $page = (int) $request->query('page', 1);
        $pageSize = (int) $request->query('pageSize', 20);

        // Gunakan API query parameter untuk search dan pagination
        $params = [
            'page' => $page,
            'pageSize' => $pageSize,
        ];

        if ($search !== '') {
            $params['search'] = $search;
        }

        $res = $api->pasien($params);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Gagal mengambil data pasien. Silakan coba lagi.';
            return view('pages.admin.pasien', [
                'patients' => [],
                'page' => $page,
                'pageSize' => $pageSize,
                'total' => 0,
                'search' => $search,
                'errorMessage' => $message,
            ]);
        }

        $json = $res->json();

        return view('pages.admin.pasien', [
            'patients' => data_get($json, 'items', []),
            'page' => data_get($json, 'page', $page),
            'pageSize' => data_get($json, 'pageSize', $pageSize),
            'total' => data_get($json, 'total', 0),
            'search' => $search,
            'errorMessage' => null,
        ]);
    }

    /**
     * Detail pasien
     * GET /patients/:id
     */
    public function show(ExpressApiService $api, int $id)
    {
        $res = $api->pasienDetail($id);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Data pasien tidak ditemukan.';
            return back()->with('error', $message);
        }

        $patient = $res->json();

        return view('pages.admin.detail-pasien', [
            'patient' => $patient,
        ]);
    }

    /**
     * Advanced search pasien
     * POST /patients/search
     */
    public function advancedSearch(Request $request, ExpressApiService $api)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:16'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dobStart' => ['nullable', 'date'],
            'dobEnd' => ['nullable', 'date', 'after_or_equal:dobStart'],
            'regStart' => ['nullable', 'date'],
            'regEnd' => ['nullable', 'date', 'after_or_equal:regStart'],
            'page' => ['nullable', 'integer', 'min:1'],
            'pageSize' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sortBy' => ['nullable', 'string', 'in:nama,nik,tgl_lahir,created_at'],
            'sortOrder' => ['nullable', 'string', 'in:ASC,DESC'],
        ], [
            'dobEnd.after_or_equal' => 'Tanggal lahir akhir harus setelah atau sama dengan tanggal lahir awal',
            'regEnd.after_or_equal' => 'Tanggal registrasi akhir harus setelah atau sama dengan tanggal registrasi awal',
            'pageSize.max' => 'Jumlah data per halaman maksimal 100',
        ]);

        // Build search criteria
        $criteria = array_filter([
            'name' => $validated['name'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'dobStart' => $validated['dobStart'] ?? null,
            'dobEnd' => $validated['dobEnd'] ?? null,
            'regStart' => $validated['regStart'] ?? null,
            'regEnd' => $validated['regEnd'] ?? null,
            'page' => $validated['page'] ?? 1,
            'pageSize' => $validated['pageSize'] ?? 20,
            'sortBy' => $validated['sortBy'] ?? 'nama',
            'sortOrder' => $validated['sortOrder'] ?? 'ASC',
        ], fn($v) => $v !== null && $v !== '');

        $res = $api->patientSearch($criteria);

        if (!$res->successful()) {
            $message = $res->json('message') ?? 'Pencarian gagal. Silakan coba lagi.';
            return view('pages.admin.pasien-search', [
                'patients' => [],
                'page' => $criteria['page'] ?? 1,
                'pageSize' => $criteria['pageSize'] ?? 20,
                'total' => 0,
                'criteria' => $criteria,
                'errorMessage' => $message,
            ]);
        }

        $json = $res->json();

        return view('pages.admin.pasien-search', [
            'patients' => data_get($json, 'items', []),
            'page' => data_get($json, 'page', 1),
            'pageSize' => data_get($json, 'pageSize', 20),
            'total' => data_get($json, 'total', 0),
            'criteria' => $criteria,
            'errorMessage' => null,
        ]);
    }

    /**
     * Show advanced search form
     */
    public function showAdvancedSearch()
    {
        return view('pages.admin.pasien-search-form');
    }
}
