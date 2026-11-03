<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 50);
            $table->unsignedBigInteger('entity_id');
            $table->enum('aksi', ['CREATE', 'UPDATE', 'DELETE', 'VALIDATE']);

            $table->foreignId('changed_by_akun_id')
                ->nullable()
                ->constrained('akun')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamp('changed_at')->useCurrent();
            $table->json('detail')->nullable();

            $table->index(['entity', 'entity_id'], 'idx_audit_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
