<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_tus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nip')->unique();

            $table->string('nama');

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ]);

            $table->string('tempat_lahir')->nullable();

            $table->date('tanggal_lahir')->nullable();

            $table->text('alamat')->nullable();

            $table->string('no_hp', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_tus');
    }
};