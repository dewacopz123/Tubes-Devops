<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemeriksaan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')
                ->constrained('pemeriksaan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('parameter_nama', 80);
            $table->string('nilai', 80);
            $table->string('satuan', 30)->nullable();
            $table->string('rujukan_min', 30)->nullable();
            $table->string('rujukan_max', 30)->nullable();
            $table->enum('flag', ['NORMAL', 'ABNORMAL', 'KRITIS'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_detail');
    }
};
