<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->default('PEMERINTAH PROVINSI NUSA TENGGARA TIMUR');
            $table->string('nama_dinas')->default('DINAS PENDIDIKAN DAN KEBUDAYAAN');
            $table->string('nama_sekolah')->default('SMA NEGERI 1 KUPANG TIMUR');
            $table->string('npsn', 20)->nullable()->default('50300123');
            $table->string('akreditasi', 10)->nullable()->default('A');
            $table->text('alamat')->nullable();
            $table->string('kelurahan')->nullable()->default('Tuatuka');
            $table->string('kecamatan')->nullable()->default('Kupang Timur');
            $table->string('kabupaten_kota')->nullable()->default('Kabupaten Kupang');
            $table->string('provinsi')->nullable()->default('Nusa Tenggara Timur');
            $table->string('kode_pos', 10)->nullable()->default('85362');
            $table->string('telepon', 30)->nullable()->default('(0380) 123456');
            $table->string('email', 100)->nullable()->default('info@sman1kupangtimur.sch.id');
            $table->string('website', 100)->nullable()->default('www.sman1kupangtimur.sch.id');

            // Kepala Sekolah
            $table->string('kepala_sekolah_nama')->nullable()->default('Drs. Yakob Manafe, M.Pd');
            $table->string('kepala_sekolah_nip', 30)->nullable()->default('197501012000011001');
            $table->string('kepala_sekolah_ttd_lokasi')->nullable()->default('Kupang Timur');

            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
