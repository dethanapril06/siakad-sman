<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        Sekolah::updateOrCreate(
            ['id' => 1],
            [
                'nama_instansi' => 'PEMERINTAH PROVINSI NUSA TENGGARA TIMUR',
                'nama_dinas' => 'DINAS PENDIDIKAN DAN KEBUDAYAAN',
                'nama_sekolah' => 'SMA NEGERI 1 KUPANG TIMUR',
                'npsn' => '50300123',
                'akreditasi' => 'A',
                'alamat' => 'Jl. Timor Raya Km. 25, Kupang Timur',
                'kelurahan' => 'Tuatuka',
                'kecamatan' => 'Kupang Timur',
                'kabupaten_kota' => 'Kabupaten Kupang',
                'provinsi' => 'Nusa Tenggara Timur',
                'kode_pos' => '85362',
                'telepon' => '(0380) 123456',
                'email' => 'info@sman1kupangtimur.sch.id',
                'website' => 'www.sman1kupangtimur.sch.id',
                'kepala_sekolah_nama' => 'Drs. Yakob Manafe, M.Pd',
                'kepala_sekolah_nip' => '197501012000011001',
                'kepala_sekolah_ttd_lokasi' => 'Kupang Timur',
            ]
        );
    }
}
