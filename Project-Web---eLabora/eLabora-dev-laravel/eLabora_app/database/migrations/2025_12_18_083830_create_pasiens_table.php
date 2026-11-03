<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_id')
                ->constrained('akun')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->char('nik', 16)->unique();
            $table->string('nama', 100);
            $table->date('tgl_lahir')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->timestamps();

            // 1 akun = 1 pasien (profil)
            $table->unique('akun_id', 'uq_pasien_akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
