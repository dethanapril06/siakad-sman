<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_akademiks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tahun_akademik_id')
                ->constrained('tahun_akademiks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('wali_kelas_id')
                ->nullable()
                ->constrained('gurus')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'kelas_id',
                'tahun_akademik_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_akademiks');
    }
};