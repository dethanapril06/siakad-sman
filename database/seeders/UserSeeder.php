<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\PegawaiTu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rolePegawaiTu = Role::where('name', 'pegawai_tu')->firstOrFail();
        $roleGuru = Role::where('name', 'guru')->firstOrFail();
        $roleKepalaSekolah = Role::where('name', 'kepala_sekolah')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Pegawai TU
        |--------------------------------------------------------------------------
        */
        $userTu = User::updateOrCreate(
            ['email' => 'tu@sman1kupangtimur.sch.id'],
            [
                'role_id' => $rolePegawaiTu->id,
                'name' => 'Pegawai Tata Usaha',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        PegawaiTu::updateOrCreate(
            ['user_id' => $userTu->id],
            [
                'nip' => '198001012005011001',
                'nama' => 'Pegawai Tata Usaha',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1980-01-01',
                'alamat' => 'Jl. Timor Raya Km. 25, Kupang Timur',
                'no_hp' => '081234567801',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Kepala Sekolah
        |--------------------------------------------------------------------------
        */
        $userKepalaSekolah = User::updateOrCreate(
            ['email' => 'kepala@sman1kupangtimur.sch.id'],
            [
                'role_id' => $roleKepalaSekolah->id,
                'name' => 'Drs. Yakob Manafe, M.Pd',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        Guru::updateOrCreate(
            ['user_id' => $userKepalaSekolah->id],
            [
                'nip' => '197501012000011001',
                'nama' => 'Drs. Yakob Manafe, M.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1975-01-01',
                'alamat' => 'Jl. El Tari No. 10, Kupang',
                'no_hp' => '081234567802',
                'status' => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Dewan Guru (Untuk semua mata pelajaran & calon Wali Kelas)
        |--------------------------------------------------------------------------
        */
        $gurus = [
            [
                'name' => 'Drs. H. Ahmad Dahlan, M.Pd.I',
                'email' => 'ahmad@sman1kupangtimur.sch.id',
                'nip' => '198001102006041005',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1980-01-10',
                'no_hp' => '081234567810',
            ],
            [
                'name' => 'Nurul Hidayah, S.Pd',
                'email' => 'nurul@sman1kupangtimur.sch.id',
                'nip' => '198203152008012015',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Ende',
                'tanggal_lahir' => '1982-03-15',
                'no_hp' => '081234567811',
            ],
            [
                'name' => 'Andreas Manu, S.Pd',
                'email' => 'andreas@sman1kupangtimur.sch.id',
                'nip' => '198903032012011003',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Atambua',
                'tanggal_lahir' => '1989-03-03',
                'no_hp' => '081234567805',
            ],
            [
                'name' => 'Budi Santoso, S.Pd, M.Pd',
                'email' => 'budi@sman1kupangtimur.sch.id',
                'nip' => '198501012010011001',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1985-01-01',
                'no_hp' => '081234567803',
            ],
            [
                'name' => 'Antonius Bau, S.Pd',
                'email' => 'antonius@sman1kupangtimur.sch.id',
                'nip' => '198405202009021008',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kefa',
                'tanggal_lahir' => '1984-05-20',
                'no_hp' => '081234567812',
            ],
            [
                'name' => 'Siti Aminah, S.Pd, M.Hum',
                'email' => 'siti@sman1kupangtimur.sch.id',
                'nip' => '198607122010012018',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1986-07-12',
                'no_hp' => '081234567813',
            ],
            [
                'name' => 'Citra Kirana, S.Sn',
                'email' => 'citra@sman1kupangtimur.sch.id',
                'nip' => '199008252014022004',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Rote',
                'tanggal_lahir' => '1990-08-25',
                'no_hp' => '081234567814',
            ],
            [
                'name' => 'Donny Frans, S.Pd.Jas',
                'email' => 'donny@sman1kupangtimur.sch.id',
                'nip' => '198811052011011009',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Alor',
                'tanggal_lahir' => '1988-11-05',
                'no_hp' => '081234567815',
            ],
            [
                'name' => 'Grace Tulle, S.T',
                'email' => 'grace@sman1kupangtimur.sch.id',
                'nip' => '199104182015032007',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1991-04-18',
                'no_hp' => '081234567816',
            ],
            [
                'name' => 'Elisabeth Kase, S.Kom, M.Cs',
                'email' => 'elisabeth@sman1kupangtimur.sch.id',
                'nip' => '198709142011012006',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Soe',
                'tanggal_lahir' => '1987-09-14',
                'no_hp' => '081234567817',
            ],
            [
                'name' => 'Maria Natalia, S.Pd, M.Si',
                'email' => 'maria@sman1kupangtimur.sch.id',
                'nip' => '198702022011012002',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Soe',
                'tanggal_lahir' => '1987-02-02',
                'no_hp' => '081234567804',
            ],
            [
                'name' => 'Hendra Wijaya, S.Si, M.Sc',
                'email' => 'hendra@sman1kupangtimur.sch.id',
                'nip' => '198306102008011012',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1983-06-10',
                'no_hp' => '081234567818',
            ],
            [
                'name' => 'Dewi Lestari, S.Si',
                'email' => 'dewi@sman1kupangtimur.sch.id',
                'nip' => '198912042014022009',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bajawa',
                'tanggal_lahir' => '1989-12-04',
                'no_hp' => '081234567819',
            ],
            [
                'name' => 'Agus Pratama, S.E, M.M',
                'email' => 'agus@sman1kupangtimur.sch.id',
                'nip' => '198102142006041007',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1981-02-14',
                'no_hp' => '081234567820',
            ],
            [
                'name' => 'Rina Melati, S.Pd',
                'email' => 'rina@sman1kupangtimur.sch.id',
                'nip' => '198805302011012014',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Waingapu',
                'tanggal_lahir' => '1988-05-30',
                'no_hp' => '081234567821',
            ],
            [
                'name' => 'Fajar Nugroho, S.Sos, M.Si',
                'email' => 'fajar@sman1kupangtimur.sch.id',
                'nip' => '198510222010011016',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1985-10-22',
                'no_hp' => '081234567822',
            ],
        ];

        foreach ($gurus as $guru) {
            $user = User::updateOrCreate(
                ['email' => $guru['email']],
                [
                    'role_id' => $roleGuru->id,
                    'name' => $guru['name'],
                    'password' => 'password',
                    'is_active' => true,
                ]
            );

            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $guru['nip'],
                    'nama' => $guru['name'],
                    'jenis_kelamin' => $guru['jenis_kelamin'],
                    'tempat_lahir' => $guru['tempat_lahir'],
                    'tanggal_lahir' => $guru['tanggal_lahir'],
                    'alamat' => 'Jl. Timor Raya, Kupang Timur',
                    'no_hp' => $guru['no_hp'],
                    'status' => 'aktif',
                ]
            );
        }
    }
}