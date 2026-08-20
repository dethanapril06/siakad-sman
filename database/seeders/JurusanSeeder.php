<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = [
            [
                'kode' => 'IPA',
                'nama' => 'Ilmu Pengetahuan Alam',
            ],
            [
                'kode' => 'IPS',
                'nama' => 'Ilmu Pengetahuan Sosial',
            ],
            [
                'kode' => 'MAT',
                'nama' => 'Matematika',
            ],
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::updateOrCreate(
                [
                    'kode' => $jurusan['kode'],
                ],
                [
                    'nama' => $jurusan['nama'],
                    'is_active' => true,
                ]
            );
        }
    }
}