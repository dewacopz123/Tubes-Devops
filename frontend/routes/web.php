<?php

use App\Models\Pasien;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardPetugasController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AntrianPasienController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\DashboardDokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\HasilPemeriksaanController;
use App\Http\Controllers\PasienDashboardController;
use App\Http\Controllers\ManajemenPasienController;
use App\Http\Controllers\PasienPemeriksaanController;
use App\Http\Controllers\DokterPemeriksaanController;

Route::get('/', function () {
    return redirect()->route('login');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// SHARED AUTHENTICATED ROUTES (accessible by all roles)
Route::middleware(['requireAuth'])->group(function () {
    Route::get('/registrations/{id}/surat-rujukan/download', [PasienPemeriksaanController::class, 'downloadSuratRujukan'])->name('registrations.download');
});

// PASIEN (wajib login + role pasien)
Route::middleware(['requireAuth', 'requireRole:PASIEN'])->group(function () {
    Route::get('/dashboard-pasien', [PasienDashboardController::class, 'index'])->name('pasien.dashboard');

    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pasien.pendaftaran.index');
    Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pasien.pendaftaran.store');

    Route::get('/antrian-pasien', [AntrianPasienController::class, 'index'])->name('antrian.pasien');

    Route::get('/hasil-pemeriksaan', [HasilPemeriksaanController::class, 'index'])->name('hasil.pemeriksaan');

    Route::get('/detail-pemeriksaan/{id}', [HasilPemeriksaanController::class, 'show'])->name('detail.pemeriksaan');
    
    Route::get('/pemeriksaan/{examId}/files/{fileId}/download', [HasilPemeriksaanController::class, 'downloadFile'])->name('pemeriksaan.file.download');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
});

// PETUGAS
Route::middleware(['requireAuth', 'requireRole:PETUGAS'])->group(function () {
    Route::get('/dashboard-petugas', [DashboardPetugasController::class, 'index'])
        ->name('dashboard.petugas');
    Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.petugas');
    Route::post('/antrian/{id}/call', [AntrianController::class, 'call'])->name('antrian.call');
    Route::post('/antrian/{id}/next', [AntrianController::class, 'next'])->name('antrian.next');
    Route::post('/antrian/{id}/cancel', [AntrianController::class, 'cancel'])->name('antrian.cancel');
    Route::get('/pemeriksaan', [PemeriksaanController::class, 'index'])
        ->name('pemeriksaan.petugas');
    Route::post('/pemeriksaan', [PemeriksaanController::class, 'store']);
    Route::patch('/pemeriksaan/{id}', [PemeriksaanController::class, 'update']);
    Route::post('/pemeriksaan/{id}/file', [PemeriksaanController::class, 'uploadFile']);
    Route::delete('/pemeriksaan/{id}', [PemeriksaanController::class, 'destroy']);
    // Manajemen Pasien
    Route::get('/pasien', [ManajemenPasienController::class, 'index'])->name('petugas.pasien');
    // Pemeriksaan milik pasien tertentu
    Route::get('/petugas/pasien/{id}/pemeriksaan', [PasienPemeriksaanController::class, 'index'])
    ->name('petugas.pasien.pemeriksaan');
    // Update exam (edit dari halaman pasien)
    Route::patch('/pasien/exams/{examId}', [PasienPemeriksaanController::class, 'updateExam'])->name('petugas.pasien.exams.update');
    // Upload file exam (dari halaman pasien)
    Route::post('/pasien/exams/{examId}/file', [PasienPemeriksaanController::class, 'uploadExamFile'])->name('petugas.pasien.exams.upload');
});
// DOKTER
Route::middleware(['requireAuth', 'requireRole:DOKTER'])->group(function () {
    Route::get('/dashboard-dokter',  [DashboardDokterController::class, 'index'])->name('dashboard.dokter');
    Route::get('/pasien-dokter', [PasienController::class, 'index'])->name('pasien.dokter');
    Route::get('/dokter/pasien/{id}/pemeriksaan', [DokterPemeriksaanController::class, 'index'])->name('dokter.pasien.pemeriksaan');
});
