<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = [
            [
                'kode' => 'R-01',
                'nama' => 'Ruang Kelas 01',
                'jenis' => 'kelas',
                'kapasitas' => 40,
            ],
            [
                'kode' => 'R-02',
                'nama' => 'Ruang Kelas 02',
                'jenis' => 'kelas',
                'kapasitas' => 40,
            ],
            [
                'kode' => 'R-03',
                'nama' => 'Ruang Kelas 03',
                'jenis' => 'kelas',
                'kapasitas' => 40,
            ],
            [
                'kode' => 'LAB-KOM',
                'nama' => 'Laboratorium Komputer',
                'jenis' => 'laboratorium',
                'kapasitas' => 40,
            ],
            [
                'kode' => 'LAB-FIS',
                'nama' => 'Laboratorium Fisika',
                'jenis' => 'laboratorium',
                'kapasitas' => 40,
            ],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::updateOrCreate(
                [
                    'kode' => $ruangan['kode'],
                ],
                [
                    'nama' => $ruangan['nama'],
                    'jenis' => $ruangan['jenis'],
                    'kapasitas' => $ruangan['kapasitas'],
                    'is_active' => true,
                ]
            );
        }
    }
}