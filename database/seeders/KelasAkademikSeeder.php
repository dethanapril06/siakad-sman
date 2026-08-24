<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasAkademik;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class KelasAkademikSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where('nama', '2026/2027')->firstOrFail();

        // Ambil guru yang bukan kepala sekolah
        $gurus = Guru::whereHas('user.role', fn ($q) => $q->where('name', 'guru'))
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get();

        $kelasList = Kelas::with('jurusan')
            ->orderByRaw("CASE tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 4 END")
            ->orderBy('jurusan_id')
            ->orderBy('nama')
            ->get();

        foreach ($kelasList as $index => $kelas) {
            // Pasangkan wali kelas unik untuk masing-masing kelas
            $waliKelas = $gurus->get($index);

            KelasAkademik::updateOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                ],
                [
                    'wali_kelas_id' => $waliKelas?->id,
                ]
            );
        }
    }
}