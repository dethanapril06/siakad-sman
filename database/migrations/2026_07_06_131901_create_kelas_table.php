<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jurusan_id')
                ->nullable()
                ->constrained('jurusans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('tingkat', [
                'X',
                'XI',
                'XII'
            ]);

            $table->string('nama');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'jurusan_id',
                'tingkat',
                'nama'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};