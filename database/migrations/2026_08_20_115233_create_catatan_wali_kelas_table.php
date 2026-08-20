<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('kelas_akademik_id')
                ->constrained('kelas_akademiks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->text('catatan')->nullable();
            $table->string('status_kenaikan')->nullable(); // e.g. "Naik ke Kelas XI", "Lulus", dll

            $table->timestamps();

            $table->unique([
                'siswa_id',
                'semester_id',
                'kelas_akademik_id'
            ], 'uniq_catatan_siswa_sem_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
    }
};
