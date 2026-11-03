<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_id')
                ->constrained('pendaftaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('dokter_id')
                ->nullable()
                ->constrained('dokter')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('petugas_lab_id')
                ->constrained('petugas_lab')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('no_lab', 30)->unique(); // aturan bisnis: unik global
            $table->dateTime('tgl_pemeriksaan')->useCurrent();
            $table->enum('status_validasi', ['DRAFT', 'TERVALIDASI'])->default('DRAFT');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->index('tgl_pemeriksaan', 'idx_pemeriksaan_tgl');

            // aturan bisnis: 1 pendaftaran = 1 kategori pemeriksaan
            $table->unique(['pendaftaran_id', 'kategori_id'], 'uq_pendaftaran_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan');
    }
};
