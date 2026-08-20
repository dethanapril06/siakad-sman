<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penilaian_id')
                ->constrained('penilaians')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('nilai', 5, 2);

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique([
                'penilaian_id',
                'siswa_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};