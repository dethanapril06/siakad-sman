<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $ipa = Jurusan::where('kode', 'IPA')->firstOrFail();
        $ips = Jurusan::where('kode', 'IPS')->firstOrFail();

        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = [$ipa, $ips];
        $rombels = ['1', '2'];

        foreach ($tingkats as $tingkat) {
            foreach ($jurusans as $jurusan) {
                foreach ($rombels as $rombel) {
                    Kelas::updateOrCreate(
                        [
                            'jurusan_id' => $jurusan->id,
                            'tingkat' => $tingkat,
                            'nama' => $rombel,
                        ],
                        [
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}