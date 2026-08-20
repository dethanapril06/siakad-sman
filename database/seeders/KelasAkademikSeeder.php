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
        $tahunAkademik = TahunAkademik::where(
            'nama',
            '2026/2027'
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

        $kelas = Kelas::with('jurusan')->get();

        foreach ($kelas as $item) {
            $waliKelasId = null;

            if (
                $item->tingkat === 'X'
                && $item->jurusan?->kode === 'IPA'
            ) {
                $waliKelasId = $guruBudi->id;
            }

            if (
                $item->tingkat === 'XI'
                && $item->jurusan?->kode === 'IPA'
            ) {
                $waliKelasId = $guruMaria->id;
            }

            if (
                $item->tingkat === 'X'
                && $item->jurusan?->kode === 'IPS'
            ) {
                $waliKelasId = $guruAndreas->id;
            }

            KelasAkademik::updateOrCreate(
                [
                    'kelas_id' => $item->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                ],
                [
                    'wali_kelas_id' => $waliKelasId,
                ]
            );
        }
    }
}