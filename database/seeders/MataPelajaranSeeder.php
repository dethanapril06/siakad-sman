<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mataPelajarans = [
            [
                'kode' => 'MTK',
                'nama' => 'Matematika',
                'kelompok' => 'wajib',
            ],
            [
                'kode' => 'BIN',
                'nama' => 'Bahasa Indonesia',
                'kelompok' => 'wajib',
            ],
            [
                'kode' => 'BIG',
                'nama' => 'Bahasa Inggris',
                'kelompok' => 'wajib',
            ],
            [
                'kode' => 'BIO',
                'nama' => 'Biologi',
                'kelompok' => 'peminatan',
            ],
            [
                'kode' => 'FIS',
                'nama' => 'Fisika',
                'kelompok' => 'peminatan',
            ],
            [
                'kode' => 'KIM',
                'nama' => 'Kimia',
                'kelompok' => 'peminatan',
            ],
            [
                'kode' => 'EKO',
                'nama' => 'Ekonomi',
                'kelompok' => 'peminatan',
            ],
            [
                'kode' => 'GEO',
                'nama' => 'Geografi',
                'kelompok' => 'peminatan',
            ],
            [
                'kode' => 'SOS',
                'nama' => 'Sosiologi',
                'kelompok' => 'peminatan',
            ],
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