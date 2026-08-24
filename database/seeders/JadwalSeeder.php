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
        $ruangans = Ruangan::all()->keyBy('kode');
        $ruanganList = Ruangan::where('jenis', 'kelas')->get()->values();

        $timeSlots = [
            ['hari' => 'senin', 'jam_mulai' => '07:30', 'jam_selesai' => '09:00'],
            ['hari' => 'senin', 'jam_mulai' => '09:15', 'jam_selesai' => '10:45'],
            ['hari' => 'senin', 'jam_mulai' => '11:00', 'jam_selesai' => '12:30'],

            ['hari' => 'selasa', 'jam_mulai' => '07:30', 'jam_selesai' => '09:00'],
            ['hari' => 'selasa', 'jam_mulai' => '09:15', 'jam_selesai' => '10:45'],
            ['hari' => 'selasa', 'jam_mulai' => '11:00', 'jam_selesai' => '12:30'],

            ['hari' => 'rabu', 'jam_mulai' => '07:30', 'jam_selesai' => '09:00'],
            ['hari' => 'rabu', 'jam_mulai' => '09:15', 'jam_selesai' => '10:45'],
            ['hari' => 'rabu', 'jam_mulai' => '11:00', 'jam_selesai' => '12:30'],

            ['hari' => 'kamis', 'jam_mulai' => '07:30', 'jam_selesai' => '09:00'],
            ['hari' => 'kamis', 'jam_mulai' => '09:15', 'jam_selesai' => '10:45'],
            ['hari' => 'kamis', 'jam_mulai' => '11:00', 'jam_selesai' => '12:30'],

            ['hari' => 'jumat', 'jam_mulai' => '07:30', 'jam_selesai' => '09:00'],
            ['hari' => 'jumat', 'jam_mulai' => '09:15', 'jam_selesai' => '10:45'],
        ];

        $mengajarByKelas = Mengajar::with(['mataPelajaran', 'kelasAkademik.kelas'])
            ->get()
            ->groupBy('kelas_akademik_id');

        $kelasCounter = 0;

        foreach ($mengajarByKelas as $kelasAkademikId => $mengajars) {
            // Tentukan ruangan utama kelas
            $homeRoom = $ruanganList->get($kelasCounter % $ruanganList->count()) ?? $ruangans->first();
            $kelasCounter++;

            foreach ($mengajars->values() as $slotIdx => $mengajar) {
                if (! isset($timeSlots[$slotIdx])) {
                    break;
                }

                $slot = $timeSlots[$slotIdx];
                $mapelKode = $mengajar->mataPelajaran->kode;

                // Tentukan lab jika relevan
                $assignedRoom = match ($mapelKode) {
                    'INF' => $ruangans->get('LAB-KOM') ?? $homeRoom,
                    'FIS' => $ruangans->get('LAB-FIS') ?? $homeRoom,
                    'BIO' => $ruangans->get('LAB-BIO') ?? $homeRoom,
                    'KIM' => $ruangans->get('LAB-KIM') ?? $homeRoom,
                    default => $homeRoom,
                };

                Jadwal::updateOrCreate(
                    [
                        'mengajar_id' => $mengajar->id,
                        'hari' => $slot['hari'],
                    ],
                    [
                        'ruangan_id' => $assignedRoom->id,
                        'jam_mulai' => $slot['jam_mulai'],
                        'jam_selesai' => $slot['jam_selesai'],
                    ]
                );
            }
        }
    }
}