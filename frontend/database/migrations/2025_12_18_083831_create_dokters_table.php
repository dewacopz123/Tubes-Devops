<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_id')
                ->constrained('akun')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nip', 30)->nullable()->index();
            $table->string('nama', 100);
            $table->timestamps();

            $table->unique('akun_id', 'uq_dokter_akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter');
    }
};
