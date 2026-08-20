<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,

            TahunAkademikSeeder::class,
            SemesterSeeder::class,

            JurusanSeeder::class,
            RuanganSeeder::class,
            MataPelajaranSeeder::class,

            UserSeeder::class,

            KelasSeeder::class,
            KelasAkademikSeeder::class,
            AnggotaKelasSeeder::class,

            MengajarSeeder::class,
            JadwalSeeder::class,

            JenisNilaiSeeder::class,
        ]);
    }
}