<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mengajars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('guru_id')
                ->constrained('gurus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('kelas_akademik_id')
                ->constrained('kelas_akademiks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'semester_id',
                'guru_id',
                'kelas_akademik_id',
                'mata_pelajaran_id'
            ], 'mengajars_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mengajars');
    }
};
