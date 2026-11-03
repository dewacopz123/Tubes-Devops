<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')
                ->constrained('pasien')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('no_antrian');
            $table->date('tgl_pendaftaran');
            $table->enum('status', ['MENUNGGU', 'DIPROSES', 'SELESAI', 'BATAL'])
                ->default('MENUNGGU');

            $table->string('surat_rujukan_path', 255)->nullable();
            $table->timestamps();

            $table->index('tgl_pendaftaran', 'idx_pendaftaran_tgl');
            $table->index('status', 'idx_pendaftaran_status');

            // aturan bisnis: no antrian unik per hari
            $table->unique(['tgl_pendaftaran', 'no_antrian'], 'uq_antrian_harian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
