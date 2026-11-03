<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemeriksaan_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')
                ->constrained('pemeriksaan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('file_path', 255);
            $table->enum('file_type', ['PDF', 'JPG', 'PNG']);
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_file');
    }
};
