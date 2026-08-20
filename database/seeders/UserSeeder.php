<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\PegawaiTu;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rolePegawaiTu = Role::where(
            'name',
            'pegawai_tu'
        )->firstOrFail();

        $roleGuru = Role::where(
            'name',
            'guru'
        )->firstOrFail();

        $roleSiswa = Role::where(
            'name',
            'siswa'
        )->firstOrFail();

        $roleKepalaSekolah = Role::where(
            'name',
            'kepala_sekolah'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Pegawai TU
        |--------------------------------------------------------------------------
        */

        $userTu = User::updateOrCreate(
            [
                'email' => 'tu@sman1kupangtimur.sch.id',
            ],
            [
                'role_id' => $rolePegawaiTu->id,
                'name' => 'Pegawai Tata Usaha',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        PegawaiTu::updateOrCreate(
            [
                'user_id' => $userTu->id,
            ],
            [
                'nip' => '198001012005011001',
                'nama' => 'Pegawai Tata Usaha',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1980-01-01',
                'alamat' => 'Kupang Timur',
                'no_hp' => '081234567801',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Kepala Sekolah
        |--------------------------------------------------------------------------
        */

        $userKepalaSekolah = User::updateOrCreate(
            [
                'email' => 'kepala@sman1kupangtimur.sch.id',
            ],
            [
                'role_id' => $roleKepalaSekolah->id,
                'name' => 'Kepala Sekolah',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        Guru::updateOrCreate(
            [
                'user_id' => $userKepalaSekolah->id,
            ],
            [
                'nip' => '197501012000011001',
                'nama' => 'Kepala Sekolah',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1975-01-01',
                'alamat' => 'Kupang Timur',
                'no_hp' => '081234567802',
                'status' => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Guru
        |--------------------------------------------------------------------------
        */

        $gurus = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@sman1kupangtimur.sch.id',
                'nip' => '198501012010011001',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1985-01-01',
                'no_hp' => '081234567803',
            ],
            [
                'name' => 'Maria Natalia',
                'email' => 'maria@sman1kupangtimur.sch.id',
                'nip' => '198702022011012002',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Soe',
                'tanggal_lahir' => '1987-02-02',
                'no_hp' => '081234567804',
            ],
            [
                'name' => 'Andreas Manu',
                'email' => 'andreas@sman1kupangtimur.sch.id',
                'nip' => '198903032012011003',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Atambua',
                'tanggal_lahir' => '1989-03-03',
                'no_hp' => '081234567805',
            ],
        ];

        foreach ($gurus as $guru) {
            $user = User::updateOrCreate(
                [
                    'email' => $guru['email'],
                ],
                [
                    'role_id' => $roleGuru->id,
                    'name' => $guru['name'],
                    'password' => 'password',
                    'is_active' => true,
                ]
            );

            Guru::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nip' => $guru['nip'],
                    'nama' => $guru['name'],
                    'jenis_kelamin' => $guru['jenis_kelamin'],
                    'tempat_lahir' => $guru['tempat_lahir'],
                    'tanggal_lahir' => $guru['tanggal_lahir'],
                    'alamat' => 'Kupang Timur',
                    'no_hp' => $guru['no_hp'],
                    'status' => 'aktif',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        $siswas = [
            ['nama' => 'Yesaya Dumanauw', 'jk' => 'L'],
            ['nama' => 'Samuel Neno', 'jk' => 'L'],
            ['nama' => 'Maria Benu', 'jk' => 'P'],
            ['nama' => 'Yohana Tallo', 'jk' => 'P'],
            ['nama' => 'Andi Kolo', 'jk' => 'L'],
            ['nama' => 'Ruth Manafe', 'jk' => 'P'],
            ['nama' => 'David Nalle', 'jk' => 'L'],
            ['nama' => 'Grace Nuban', 'jk' => 'P'],
            ['nama' => 'Yosua Fanggidae', 'jk' => 'L'],
            ['nama' => 'Ester Ndun', 'jk' => 'P'],
        ];

        foreach ($siswas as $index => $siswa) {
            $nomor = str_pad(
                (string) ($index + 1),
                3,
                '0',
                STR_PAD_LEFT
            );

            $user = User::updateOrCreate(
                [
                    'email' => "siswa{$nomor}@sman1kupangtimur.sch.id",
                ],
                [
                    'role_id' => $roleSiswa->id,
                    'name' => $siswa['nama'],
                    'password' => 'password',
                    'is_active' => true,
                ]
            );

            Siswa::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nis' => "2026{$nomor}",
                    'nisn' => "0060000{$nomor}",
                    'nama' => $siswa['nama'],
                    'jenis_kelamin' => $siswa['jk'],
                    'tempat_lahir' => 'Kupang',
                    'tanggal_lahir' => '2010-01-01',
                    'alamat' => 'Kupang Timur',
                    'nama_orang_tua' => "Orang Tua {$siswa['nama']}",
                    'no_hp_orang_tua' => "081300000{$nomor}",
                    'status' => 'aktif',
                ]
            );
        }
    }
}