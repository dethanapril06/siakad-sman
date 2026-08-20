<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Mengajar;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $ruangan = Ruangan::where(
            'kode',
            'R-01'
        )->firstOrFail();

        $mengajars = Mengajar::with(
            'mataPelajaran'
        )->get();

        foreach ($mengajars as $mengajar) {
            $jadwal = match (
                $mengajar->mataPelajaran->kode
            ) {
                'MTK' => [
                    'hari' => 'senin',
                    'jam_mulai' => '07:30',
                    'jam_selesai' => '09:00',
                ],

                'BIO' => [
                    'hari' => 'selasa',
                    'jam_mulai' => '07:30',
                    'jam_selesai' => '09:00',
                ],

                'BIN' => [
                    'hari' => 'rabu',
                    'jam_mulai' => '09:15',
                    'jam_selesai' => '10:45',
                ],

                default => null,
            };

            if (! $jadwal) {
                continue;
            }

            Jadwal::updateOrCreate(
                [
                    'mengajar_id' => $mengajar->id,
                    'hari' => $jadwal['hari'],
                ],
                [
                    'ruangan_id' => $ruangan->id,
                    'jam_mulai' => $jadwal['jam_mulai'],
                    'jam_selesai' => $jadwal['jam_selesai'],
                ]
            );
        }
    }
}