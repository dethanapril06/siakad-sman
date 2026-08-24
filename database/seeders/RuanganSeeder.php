<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = [
            ['kode' => 'R-01', 'nama' => 'Ruang Kelas X IPA 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-02', 'nama' => 'Ruang Kelas X IPA 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-03', 'nama' => 'Ruang Kelas X IPS 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-04', 'nama' => 'Ruang Kelas X IPS 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-05', 'nama' => 'Ruang Kelas XI IPA 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-06', 'nama' => 'Ruang Kelas XI IPA 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-07', 'nama' => 'Ruang Kelas XI IPS 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-08', 'nama' => 'Ruang Kelas XI IPS 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-09', 'nama' => 'Ruang Kelas XII IPA 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-10', 'nama' => 'Ruang Kelas XII IPA 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-11', 'nama' => 'Ruang Kelas XII IPS 1', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'R-12', 'nama' => 'Ruang Kelas XII IPS 2', 'jenis' => 'kelas', 'kapasitas' => 36],
            ['kode' => 'LAB-KOM', 'nama' => 'Laboratorium Komputer', 'jenis' => 'laboratorium', 'kapasitas' => 40],
            ['kode' => 'LAB-FIS', 'nama' => 'Laboratorium Fisika', 'jenis' => 'laboratorium', 'kapasitas' => 40],
            ['kode' => 'LAB-BIO', 'nama' => 'Laboratorium Biologi', 'jenis' => 'laboratorium', 'kapasitas' => 40],
            ['kode' => 'LAB-KIM', 'nama' => 'Laboratorium Kimia', 'jenis' => 'laboratorium', 'kapasitas' => 40],
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