<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertemuans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mengajar_id')
                ->constrained('mengajars')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('pertemuan_ke');

            $table->date('tanggal');

            $table->time('jam_mulai')->nullable();

            $table->time('jam_selesai')->nullable();

            $table->text('materi')->nullable();

            $table->timestamps();

            $table->unique([
                'mengajar_id',
                'pertemuan_ke'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertemuans');
    }
};