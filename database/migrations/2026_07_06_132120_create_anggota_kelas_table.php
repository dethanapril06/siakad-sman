<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kelas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_akademik_id')
                ->constrained('kelas_akademiks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'kelas_akademik_id',
                'siswa_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kelas');
    }
};