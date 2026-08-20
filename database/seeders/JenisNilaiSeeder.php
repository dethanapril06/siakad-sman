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
            ],
            [
                'kode' => 'TUGAS',
                'nama' => 'Tugas',
            ],
            [
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
            ],
            [
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
            ],
        ];

        foreach ($jenisNilais as $jenisNilai) {
            JenisNilai::updateOrCreate(
                [
                    'kode' => $jenisNilai['kode'],
                ],
                [
                    'nama' => $jenisNilai['nama'],
                    'is_active' => true,
                ]
            );
        }
    }
}