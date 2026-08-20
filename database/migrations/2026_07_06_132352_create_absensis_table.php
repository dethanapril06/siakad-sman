<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pertemuan_id')
                ->constrained('pertemuans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', [
                'hadir',
                'sakit',
                'izin',
                'alpa',
                'terlambat'
            ]);

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique([
                'pertemuan_id',
                'siswa_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};