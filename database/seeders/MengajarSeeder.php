<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Mengajar;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class MengajarSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where(
            'is_active',
            true
        )->firstOrFail();

        $kelas = Kelas::where('tingkat', 'X')
            ->whereHas('jurusan', function ($query) {
                $query->where('kode', 'IPA');
            })
            ->firstOrFail();

        $kelasAkademik = KelasAkademik::where(
            'kelas_id',
            $kelas->id
        )->firstOrFail();

        $guruBudi = Guru::where(
            'nip',
            '198501012010011001'
        )->firstOrFail();

        $guruMaria = Guru::where(
            'nip',
            '198702022011012002'
        )->firstOrFail();

        $guruAndreas = Guru::where(
            'nip',
            '198903032012011003'
        )->firstOrFail();

        $matematika = MataPelajaran::where(
            'kode',
            'MTK'
        )->firstOrFail();

        $biologi = MataPelajaran::where(
            'kode',
            'BIO'
        )->firstOrFail();

        $bahasaIndonesia = MataPelajaran::where(
            'kode',
            'BIN'
        )->firstOrFail();

        Mengajar::updateOrCreate([
            'semester_id' => $semester->id,
            'guru_id' => $guruBudi->id,
            'kelas_akademik_id' => $kelasAkademik->id,
            'mata_pelajaran_id' => $matematika->id,
        ]);

        Mengajar::updateOrCreate([
            'semester_id' => $semester->id,
            'guru_id' => $guruMaria->id,
            'kelas_akademik_id' => $kelasAkademik->id,
            'mata_pelajaran_id' => $biologi->id,
        ]);

        Mengajar::updateOrCreate([
            'semester_id' => $semester->id,
            'guru_id' => $guruAndreas->id,
            'kelas_akademik_id' => $kelasAkademik->id,
            'mata_pelajaran_id' => $bahasaIndonesia->id,
        ]);
    }
}