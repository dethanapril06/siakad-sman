<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Mengajar;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class MengajarSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('is_active', true)->firstOrFail();

        // Pemetaan Mata Pelajaran ke Guru Spesialis
        $mapelGuruMapping = [
            'PAI'  => 'ahmad@sman1kupangtimur.sch.id',
            'PPKN' => 'nurul@sman1kupangtimur.sch.id',
            'BIN'  => 'andreas@sman1kupangtimur.sch.id',
            'MTK'  => 'budi@sman1kupangtimur.sch.id',
            'SEJ'  => 'antonius@sman1kupangtimur.sch.id',
            'BIG'  => 'siti@sman1kupangtimur.sch.id',
            'SNB'  => 'citra@sman1kupangtimur.sch.id',
            'PJK'  => 'donny@sman1kupangtimur.sch.id',
            'PKR'  => 'grace@sman1kupangtimur.sch.id',
            'INF'  => 'elisabeth@sman1kupangtimur.sch.id',
            'BIO'  => 'maria@sman1kupangtimur.sch.id',
            'FIS'  => 'hendra@sman1kupangtimur.sch.id',
            'KIM'  => 'dewi@sman1kupangtimur.sch.id',
            'EKO'  => 'agus@sman1kupangtimur.sch.id',
            'GEO'  => 'rina@sman1kupangtimur.sch.id',
            'SOS'  => 'fajar@sman1kupangtimur.sch.id',
        ];

        $guruModels = [];
        foreach ($mapelGuruMapping as $kodeMapel => $emailGuru) {
            $guru = Guru::whereHas('user', fn ($q) => $q->where('email', $emailGuru))->first();
            if ($guru) {
                $guruModels[$kodeMapel] = $guru;
            }
        }

        $mapelModels = MataPelajaran::all()->keyBy('kode');

        $mapelUmum = ['PAI', 'PPKN', 'BIN', 'MTK', 'SEJ', 'BIG', 'SNB', 'PJK', 'PKR', 'INF'];
        $mapelIpa  = ['BIO', 'FIS', 'KIM'];
        $mapelIps  = ['EKO', 'GEO', 'SOS'];

        $kelasAkademiks = KelasAkademik::with('kelas.jurusan')->get();

        foreach ($kelasAkademiks as $ka) {
            $jurusanKode = $ka->kelas?->jurusan?->kode;
            $targetMapels = $mapelUmum;

            if ($jurusanKode === 'IPA') {
                $targetMapels = array_merge($targetMapels, $mapelIpa);
            } elseif ($jurusanKode === 'IPS') {
                $targetMapels = array_merge($targetMapels, $mapelIps);
            }

            foreach ($targetMapels as $kodeMapel) {
                $mapel = $mapelModels->get($kodeMapel);
                $guru  = $guruModels[$kodeMapel] ?? null;

                if (! $mapel || ! $guru) {
                    continue;
                }

                Mengajar::updateOrCreate(
                    [
                        'semester_id' => $semester->id,
                        'guru_id' => $guru->id,
                        'kelas_akademik_id' => $ka->id,
                        'mata_pelajaran_id' => $mapel->id,
                    ]
                );
            }
        }
    }
}