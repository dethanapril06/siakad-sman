<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_akademik_id')
                ->constrained('tahun_akademiks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('nama', [
                'ganjil',
                'genap'
            ]);

            $table->date('tanggal_mulai');

            $table->date('tanggal_selesai');

            $table->boolean('is_active')->default(false);

            $table->timestamps();

            $table->unique([
                'tahun_akademik_id',
                'nama'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};