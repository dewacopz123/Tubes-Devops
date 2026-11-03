<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Akun;
use App\Models\AuditLog;
use App\Models\Dokter;
use App\Models\Kategori;
use App\Models\Pasien;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PemeriksaanFile;
use App\Models\Pendaftaran;
use App\Models\PetugasLab;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelsTest extends TestCase
{
    public function test_akun_model_attributes_and_relations(): void
    {
        $akun = new Akun();
        $this->assertEquals('akun', $akun->getTable());
        $this->assertContains('username', $akun->getFillable());
        $this->assertContains('email', $akun->getFillable());
        $this->assertInstanceOf(HasOne::class, $akun->pasien());
        $this->assertInstanceOf(HasOne::class, $akun->dokter());
        $this->assertInstanceOf(HasOne::class, $akun->petugasLab());
        $this->assertInstanceOf(HasMany::class, $akun->auditLogs());
    }

    public function test_audit_log_model(): void
    {
        $log = new AuditLog();
        $this->assertEquals('audit_log', $log->getTable());
        $this->assertFalse($log->timestamps);
        $this->assertContains('entity', $log->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $log->changedBy());
    }

    public function test_dokter_model(): void
    {
        $dokter = new Dokter();
        $this->assertEquals('dokter', $dokter->getTable());
        $this->assertContains('nip', $dokter->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $dokter->akun());
        $this->assertInstanceOf(HasMany::class, $dokter->pemeriksaan());
    }

    public function test_kategori_model(): void
    {
        $kategori = new Kategori();
        $this->assertEquals('kategori', $kategori->getTable());
        $this->assertContains('nama', $kategori->getFillable());
        $this->assertInstanceOf(HasMany::class, $kategori->pemeriksaan());
    }

    public function test_pasien_model(): void
    {
        $pasien = new Pasien();
        $this->assertEquals('pasien', $pasien->getTable());
        $this->assertContains('nik', $pasien->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $pasien->akun());
        $this->assertInstanceOf(HasMany::class, $pasien->pendaftaran());
    }

    public function test_pemeriksaan_model(): void
    {
        $exam = new Pemeriksaan();
        $this->assertEquals('pemeriksaan', $exam->getTable());
        $this->assertContains('no_lab', $exam->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $exam->pendaftaran());
        $this->assertInstanceOf(BelongsTo::class, $exam->kategori());
        $this->assertInstanceOf(BelongsTo::class, $exam->dokter());
        $this->assertInstanceOf(BelongsTo::class, $exam->petugasLab());
        $this->assertInstanceOf(HasMany::class, $exam->detail());
        $this->assertInstanceOf(HasMany::class, $exam->files());
    }

    public function test_pemeriksaan_detail_model(): void
    {
        $detail = new PemeriksaanDetail();
        $this->assertEquals('pemeriksaan_detail', $detail->getTable());
        $this->assertContains('parameter_nama', $detail->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $detail->pemeriksaan());
    }

    public function test_pemeriksaan_file_model(): void
    {
        $file = new PemeriksaanFile();
        $this->assertEquals('pemeriksaan_file', $file->getTable());
        $this->assertFalse($file->timestamps);
        $this->assertContains('file_path', $file->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $file->pemeriksaan());
    }

    public function test_pendaftaran_model(): void
    {
        $pendaftaran = new Pendaftaran();
        $this->assertEquals('pendaftaran', $pendaftaran->getTable());
        $this->assertContains('no_antrian', $pendaftaran->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $pendaftaran->pasien());
        $this->assertInstanceOf(HasMany::class, $pendaftaran->pemeriksaan());
    }

    public function test_petugas_lab_model(): void
    {
        $petugas = new PetugasLab();
        $this->assertEquals('petugas_lab', $petugas->getTable());
        $this->assertContains('nip', $petugas->getFillable());
        $this->assertInstanceOf(BelongsTo::class, $petugas->akun());
        $this->assertInstanceOf(HasMany::class, $petugas->pemeriksaan());
    }
}

