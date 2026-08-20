<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where(
            'nama',
            '2026/2027'
        )->firstOrFail();

        Semester::updateOrCreate(
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'ganjil',
            ],
            [
                'tanggal_mulai' => '2026-07-13',
                'tanggal_selesai' => '2026-12-19',
                'is_active' => true,
            ]
        );

        Semester::updateOrCreate(
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama' => 'genap',
            ],
            [
                'tanggal_mulai' => '2027-01-04',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => false,
            ]
        );
    }
}