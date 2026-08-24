<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mataPelajarans = [
            // Kelompok Wajib (Umum)
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama & Budi Pekerti', 'kelompok' => 'wajib'],
            ['kode' => 'PPKN', 'nama' => 'Pendidikan Pancasila & Kewarganegaraan', 'kelompok' => 'wajib'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'kelompok' => 'wajib'],
            ['kode' => 'MTK', 'nama' => 'Matematika (Wajib)', 'kelompok' => 'wajib'],
            ['kode' => 'SEJ', 'nama' => 'Sejarah Indonesia', 'kelompok' => 'wajib'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'kelompok' => 'wajib'],
            ['kode' => 'SNB', 'nama' => 'Seni Budaya', 'kelompok' => 'wajib'],
            ['kode' => 'PJK', 'nama' => 'Pendidikan Jasmani, Olahraga & Kesehatan', 'kelompok' => 'wajib'],
            ['kode' => 'PKR', 'nama' => 'Prakarya dan Kewirausahaan', 'kelompok' => 'wajib'],
            ['kode' => 'INF', 'nama' => 'Informatika', 'kelompok' => 'wajib'],

            // Kelompok Peminatan MIPA (IPA)
            ['kode' => 'BIO', 'nama' => 'Biologi', 'kelompok' => 'peminatan'],
            ['kode' => 'FIS', 'nama' => 'Fisika', 'kelompok' => 'peminatan'],
            ['kode' => 'KIM', 'nama' => 'Kimia', 'kelompok' => 'peminatan'],

            // Kelompok Peminatan IPS
            ['kode' => 'EKO', 'nama' => 'Ekonomi', 'kelompok' => 'peminatan'],
            ['kode' => 'GEO', 'nama' => 'Geografi', 'kelompok' => 'peminatan'],
            ['kode' => 'SOS', 'nama' => 'Sosiologi', 'kelompok' => 'peminatan'],
        ];

        foreach ($mataPelajarans as $mataPelajaran) {
            MataPelajaran::updateOrCreate(
                [
                    'kode' => $mataPelajaran['kode'],
                ],
                [
                    'nama' => $mataPelajaran['nama'],
                    'kelompok' => $mataPelajaran['kelompok'],
                    'is_active' => true,
                ]
            );
        }
    }
}