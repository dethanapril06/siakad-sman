<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $ipa = Jurusan::where('kode', 'IPA')
            ->firstOrFail();

        $ips = Jurusan::where('kode', 'IPS')
            ->firstOrFail();

        $mat = Jurusan::where('kode', 'MAT')
            ->firstOrFail();

        $kelas = [
            [
                'jurusan_id' => $ipa->id,
                'tingkat' => 'X',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $ipa->id,
                'tingkat' => 'XI',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $ipa->id,
                'tingkat' => 'XII',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $ips->id,
                'tingkat' => 'X',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $ips->id,
                'tingkat' => 'XI',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $ips->id,
                'tingkat' => 'XII',
                'nama' => '1',
            ],
            [
                'jurusan_id' => $mat->id,
                'tingkat' => 'X',
                'nama' => '1',
            ],
        ];

        foreach ($kelas as $item) {
            Kelas::updateOrCreate(
                [
                    'jurusan_id' => $item['jurusan_id'],
                    'tingkat' => $item['tingkat'],
                    'nama' => $item['nama'],
                ],
                [
                    'is_active' => true,
                ]
            );
        }
    }
}