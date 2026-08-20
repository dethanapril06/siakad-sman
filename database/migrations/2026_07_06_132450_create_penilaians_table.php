<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mengajar_id')
                ->constrained('mengajars')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('jenis_nilai_id')
                ->constrained('jenis_nilais')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('judul');

            $table->date('tanggal');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};