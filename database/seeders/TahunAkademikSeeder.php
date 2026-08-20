<?php

namespace Database\Seeders;

use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    public function run(): void
    {
        TahunAkademik::updateOrCreate(
            [
                'nama' => '2025/2026',
            ],
            [
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => false,
            ]
        );

        TahunAkademik::updateOrCreate(
            [
                'nama' => '2026/2027',
            ],
            [
                'tanggal_mulai' => '2026-07-13',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]
        );
    }
}