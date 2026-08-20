<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'pegawai_tu',
            'guru',
            'siswa',
            'kepala_sekolah',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                [
                    'name' => $role,
                ],
                [
                    'name' => $role,
                ]
            );
        }
    }
}