<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mengajar_id')
                ->constrained('mengajars')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ruangan_id')
                ->nullable()
                ->constrained('ruangans')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('hari', [
                'senin',
                'selasa',
                'rabu',
                'kamis',
                'jumat',
                'sabtu'
            ]);

            $table->time('jam_mulai');

            $table->time('jam_selesai');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};