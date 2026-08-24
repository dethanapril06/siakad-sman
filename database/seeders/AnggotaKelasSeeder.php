<?php

namespace Database\Seeders;

use App\Models\AnggotaKelas;
use App\Models\KelasAkademik;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnggotaKelasSeeder extends Seeder
{
    public function run(): void
    {
        $roleSiswa = Role::where('name', 'siswa')->firstOrFail();
        $tahunAkademik = TahunAkademik::where('nama', '2026/2027')->firstOrFail();

        $kelasAkademiks = KelasAkademik::with('kelas.jurusan')
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->get();

        // Kumpulan nama depan & belakang untuk menghasilkan 10 nama realistis per kelas
        $namaLaki = [
            'Yesaya Dumanauw', 'Samuel Neno', 'Andi Kolo', 'David Nalle', 'Yosua Fanggidae',
            'Gabriel Mauk', 'Daniel Bire', 'Mikael Tefa', 'Nathaniel Lomi', 'Christian Seran',
            'Stefanus Taopan', 'Markus Baun', 'Yoseph Oematan', 'Albertus Rassi', 'Jonathan Pellokila',
            'Petrus Kause', 'Lukas Manek', 'Titus Sabat', 'Efraim Hauteas', 'Paulus Bessie',
            'Benyamin Leo', 'Immanuel Tnunay', 'Ruben Missa', 'Simon Banunaek', 'Eliezer Selan'
        ];

        $namaPerempuan = [
            'Maria Benu', 'Yohana Tallo', 'Ruth Manafe', 'Grace Nuban', 'Ester Ndun',
            'Debora Kase', 'Elisabeth Lado', 'Priskila Hau', 'Sarah Dethan', 'Rebeka Haba',
            'Martha Toelle', 'Naomi Snae', 'Kezia Bire', 'Lydia Sanu', 'Tabita Koen',
            'Miriam Fay', 'Hanna Liu', 'Salome Koro', 'Eunike Tefa', 'Abigail Nalle',
            'Phebe Saban', 'Dorcas Lake', 'Claudia Sinlae', 'Rachel Tobe', 'Damaris Boimau'
        ];

        $globalCounter = 1;

        foreach ($kelasAkademiks as $kIdx => $ka) {
            $tingkat = $ka->kelas->tingkat;
            $jurusanKode = strtolower($ka->kelas->jurusan?->kode ?? 'gen');
            $rombel = $ka->kelas->nama;
            $kodeKelas = strtolower("{$tingkat}_{$jurusanKode}_{$rombel}");

            for ($i = 1; $i <= 10; $i++) {
                $isLaki = ($i % 2 !== 0);
                $namePool = $isLaki ? $namaLaki : $namaPerempuan;
                $nameBase = $namePool[($kIdx * 2 + $i) % count($namePool)];
                $namaLengkap = "{$nameBase} " . chr(64 + ($kIdx % 26 + 1));

                $padIndex = str_pad((string) $globalCounter, 3, '0', STR_PAD_LEFT);
                $email = "siswa{$padIndex}@sman1kupangtimur.sch.id";
                $nis = "2026" . str_pad((string) $globalCounter, 4, '0', STR_PAD_LEFT);
                $nisn = "006" . str_pad((string) $globalCounter, 7, '0', STR_PAD_LEFT);

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role_id' => $roleSiswa->id,
                        'name' => $namaLengkap,
                        'password' => 'password',
                        'is_active' => true,
                    ]
                );

                $siswa = Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'nama' => $namaLengkap,
                        'jenis_kelamin' => $isLaki ? 'L' : 'P',
                        'tempat_lahir' => 'Kupang',
                        'tanggal_lahir' => match ($tingkat) {
                            'XII' => '2008-05-15',
                            'XI' => '2009-06-20',
                            default => '2010-07-10',
                        },
                        'alamat' => 'Kupang Timur',
                        'nama_orang_tua' => "Orang Tua {$namaLengkap}",
                        'no_hp_orang_tua' => '0813' . str_pad((string) $globalCounter, 8, '0', STR_PAD_LEFT),
                        'status' => 'aktif',
                    ]
                );

                AnggotaKelas::updateOrCreate(
                    [
                        'kelas_akademik_id' => $ka->id,
                        'siswa_id' => $siswa->id,
                    ]
                );

                $globalCounter++;
            }
        }
    }
}