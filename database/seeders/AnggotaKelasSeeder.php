<?php

namespace Database\Seeders;

use App\Models\AnggotaKelas;
use App\Models\Kelas;
use App\Models\KelasAkademik;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class AnggotaKelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademik = TahunAkademik::where(
            'nama',
            '2026/2027'
        )->firstOrFail();

        $kelas = Kelas::where('tingkat', 'X')
            ->whereHas('jurusan', function ($query) {
                $query->where('kode', 'IPA');
            })
            ->firstOrFail();

        $kelasAkademik = KelasAkademik::where(
            'kelas_id',
            $kelas->id
        )
            ->where(
                'tahun_akademik_id',
                $tahunAkademik->id
            )
            ->firstOrFail();

        $siswas = Siswa::where(
            'status',
            'aktif'
        )->get();

        foreach ($siswas as $siswa) {
            AnggotaKelas::updateOrCreate([
                'kelas_akademik_id' => $kelasAkademik->id,
                'siswa_id' => $siswa->id,
            ]);
        }
    }
}