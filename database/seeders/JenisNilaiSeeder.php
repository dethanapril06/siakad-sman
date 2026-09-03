<?php

namespace Database\Seeders;

use App\Models\JenisNilai;
use Illuminate\Database\Seeder;

class JenisNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $jenisNilais = [
            [
                'kode' => 'NH',
                'nama' => 'Nilai Harian',
                'bobot' => 20,
                'urutan' => 1,
            ],
            [
                'kode' => 'TUGAS',
                'nama' => 'Tugas',
                'bobot' => 20,
                'urutan' => 2,
            ],
            [
                'kode' => 'KTR',
                'nama' => 'Keterampilan',
                'bobot' => 20,
                'urutan' => 3,
            ],
            [
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
                'bobot' => 20,
                'urutan' => 4,
            ],
            [
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
                'bobot' => 20,
                'urutan' => 5,
            ],
        ];

        foreach ($jenisNilais as $jenisNilai) {
            JenisNilai::updateOrCreate(
                [
                    'kode' => $jenisNilai['kode'],
                ],
                [
                    'nama' => $jenisNilai['nama'],
                    'bobot' => $jenisNilai['bobot'],
                    'urutan' => $jenisNilai['urutan'],
                    'is_active' => true,
                ]
            );
        }
    }
}