<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();

            $table->string('nama');

            $table->enum('jenis', [
                'kelas',
                'laboratorium',
                'lainnya'
            ])->default('kelas');

            $table->unsignedInteger('kapasitas')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};