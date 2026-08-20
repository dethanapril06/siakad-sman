<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\JenisNilai;
use App\Models\KelasAkademik;
use App\Models\Mengajar;
use App\Models\Penilaian;
use App\Models\Pertemuan;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Support\Collection;

class DashboardMonitoringService
{
    /**
     * Mengambil seluruh data dashboard monitoring.
     */
    public function getDashboardData(): array
    {
        $tahunAkademikAktif = TahunAkademik::aktif()
            ->first();

        $semesterAktif = Semester::aktif()
            ->with('tahunAkademik')
            ->first();

        return [
            'tahunAkademikAktif' => $tahunAkademikAktif,
            'semesterAktif' => $semesterAktif,

            'ringkasan' => $this->getRingkasan(
                $tahunAkademikAktif?->id,
                $semesterAktif?->id
            ),

            'progresAbsensi' => $this->getProgresAbsensi(
                $semesterAktif?->id
            ),

            'progresNilai' => $this->getProgresNilai(
                $semesterAktif?->id
            ),

            'pertemuanTerbaru' => $this->getPertemuanTerbaru(
                $semesterAktif?->id
            ),

            'penilaianTerbaru' => $this->getPenilaianTerbaru(
                $semesterAktif?->id
            ),
        ];
    }

    /**
     * Ringkasan statistik utama.
     */
    private function getRingkasan(
        ?int $tahunAkademikId,
        ?int $semesterId
    ): array {
        $jumlahSiswaAktif = Siswa::where(
            'status',
            'aktif'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Guru Aktif
        |--------------------------------------------------------------------------
        |
        | Hanya role guru yang dihitung.
        | Kepala sekolah memiliki profil Guru, tetapi tidak dihitung
        | sebagai Guru Mapel pada card ini.
        |
        */

        $jumlahGuruAktif = Guru::where(
            'status',
            'aktif'
        )
            ->whereHas(
                'user',
                function ($query) {
                    $query
                        ->where('is_active', true)
                        ->whereHas(
                            'role',
                            fn ($query) => $query->where(
                                'name',
                                'guru'
                            )
                        );
                }
            )
            ->count();

        $jumlahKelasAktif = $tahunAkademikId
            ? KelasAkademik::where(
                'tahun_akademik_id',
                $tahunAkademikId
            )->count()
            : 0;

        $jumlahPenugasanMengajar = $semesterId
            ? Mengajar::where(
                'semester_id',
                $semesterId
            )->count()
            : 0;

        $jumlahPertemuan = $semesterId
            ? Pertemuan::whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )->count()
            : 0;

        $jumlahPenilaian = $semesterId
            ? Penilaian::whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )->count()
            : 0;

        $rekapKehadiran = $this->getRekapKehadiran(
            $semesterId
        );

        return [
            'jumlah_siswa_aktif' =>
                $jumlahSiswaAktif,

            'jumlah_guru_aktif' =>
                $jumlahGuruAktif,

            'jumlah_kelas_aktif' =>
                $jumlahKelasAktif,

            'jumlah_penugasan_mengajar' =>
                $jumlahPenugasanMengajar,

            'jumlah_pertemuan' =>
                $jumlahPertemuan,

            'jumlah_penilaian' =>
                $jumlahPenilaian,

            'total_absensi' =>
                $rekapKehadiran['total'],

            'hadir' =>
                $rekapKehadiran['hadir'],

            'sakit' =>
                $rekapKehadiran['sakit'],

            'izin' =>
                $rekapKehadiran['izin'],

            'alpa' =>
                $rekapKehadiran['alpa'],

            'terlambat' =>
                $rekapKehadiran['terlambat'],

            'persentase_kehadiran' =>
                $rekapKehadiran[
                    'persentase_kehadiran'
                ],
        ];
    }

    /**
     * Rekap kehadiran semester aktif.
     */
    private function getRekapKehadiran(
        ?int $semesterId
    ): array {
        if (! $semesterId) {
            return [
                'total' => 0,
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'terlambat' => 0,
                'persentase_kehadiran' => 0,
            ];
        }

        $absensis = Absensi::whereHas(
            'pertemuan.mengajar',
            fn ($query) => $query->where(
                'semester_id',
                $semesterId
            )
        )->get([
            'id',
            'status',
        ]);

        $total = $absensis->count();

        $hadir = $absensis
            ->where('status', 'hadir')
            ->count();

        $sakit = $absensis
            ->where('status', 'sakit')
            ->count();

        $izin = $absensis
            ->where('status', 'izin')
            ->count();

        $alpa = $absensis
            ->where('status', 'alpa')
            ->count();

        $terlambat = $absensis
            ->where('status', 'terlambat')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Hadir + Terlambat Tetap Dihitung Kehadiran
        |--------------------------------------------------------------------------
        */

        $jumlahKehadiran = $hadir
            + $terlambat;

        $persentaseKehadiran = $total > 0
            ? round(
                (
                    $jumlahKehadiran
                    / $total
                ) * 100,
                2
            )
            : 0;

        return [
            'total' => $total,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'terlambat' => $terlambat,
            'persentase_kehadiran' =>
                $persentaseKehadiran,
        ];
    }

    /**
     * Progres pengisian absensi setiap penugasan mengajar.
     */
    private function getProgresAbsensi(
        ?int $semesterId
    ): Collection {
        if (! $semesterId) {
            return collect();
        }

        $mengajars = Mengajar::with([
            'guru',
            'mataPelajaran',
            'kelasAkademik.kelas.jurusan',
            'kelasAkademik.anggotaKelas',
            'pertemuans.absensis',
        ])
            ->where(
                'semester_id',
                $semesterId
            )
            ->get();

        return $mengajars
            ->map(function (Mengajar $mengajar) {
                $jumlahSiswa = $mengajar
                    ->kelasAkademik
                    ->anggotaKelas
                    ->count();

                $jumlahPertemuan = $mengajar
                    ->pertemuans
                    ->count();

                $pertemuanLengkap = $mengajar
                    ->pertemuans
                    ->filter(
                        function (
                            Pertemuan $pertemuan
                        ) use ($jumlahSiswa) {
                            if ($jumlahSiswa === 0) {
                                return false;
                            }

                            return $pertemuan
                                ->absensis
                                ->count()
                                === $jumlahSiswa;
                        }
                    )
                    ->count();

                $pertemuanBelumLengkap =
                    $jumlahPertemuan
                    - $pertemuanLengkap;

                $persentase = $jumlahPertemuan > 0
                    ? round(
                        (
                            $pertemuanLengkap
                            / $jumlahPertemuan
                        ) * 100,
                        2
                    )
                    : 0;

                return [
                    'mengajar_id' => $mengajar->id,

                    'guru' => $mengajar->guru,

                    'mata_pelajaran' =>
                        $mengajar->mataPelajaran,

                    'kelas_akademik' =>
                        $mengajar->kelasAkademik,

                    'jumlah_siswa' =>
                        $jumlahSiswa,

                    'jumlah_pertemuan' =>
                        $jumlahPertemuan,

                    'pertemuan_lengkap' =>
                        $pertemuanLengkap,

                    'pertemuan_belum_lengkap' =>
                        $pertemuanBelumLengkap,

                    'persentase' =>
                        $persentase,

                    'status' => $this
                        ->statusProgres($persentase),
                ];
            })
            ->sortBy('persentase')
            ->values();
    }

    /**
     * Progres pengisian nilai setiap penugasan mengajar.
     */
    private function getProgresNilai(
        ?int $semesterId
    ): Collection {
        if (! $semesterId) {
            return collect();
        }

        $jenisNilais = JenisNilai::aktif()
            ->orderBy('id')
            ->get();

        $mengajars = Mengajar::with([
            'guru',
            'mataPelajaran',
            'kelasAkademik.kelas.jurusan',
            'kelasAkademik.anggotaKelas',
            'penilaians.jenisNilai',
            'penilaians.nilais',
        ])
            ->where(
                'semester_id',
                $semesterId
            )
            ->get();

        return $mengajars
            ->map(function (
                Mengajar $mengajar
            ) use ($jenisNilais) {
                $jumlahSiswa = $mengajar
                    ->kelasAkademik
                    ->anggotaKelas
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Progres Penilaian yang Sudah Dibuat
                |--------------------------------------------------------------------------
                */

                $jumlahPenilaian = $mengajar
                    ->penilaians
                    ->count();

                $penilaianLengkap = $mengajar
                    ->penilaians
                    ->filter(
                        function (
                            Penilaian $penilaian
                        ) use ($jumlahSiswa) {
                            if ($jumlahSiswa === 0) {
                                return false;
                            }

                            return $penilaian
                                ->nilais
                                ->count()
                                === $jumlahSiswa;
                        }
                    )
                    ->count();

                $penilaianBelumLengkap =
                    $jumlahPenilaian
                    - $penilaianLengkap;

                $persentase = $jumlahPenilaian > 0
                    ? round(
                        (
                            $penilaianLengkap
                            / $jumlahPenilaian
                        ) * 100,
                        2
                    )
                    : 0;

                /*
                |--------------------------------------------------------------------------
                | Status Komponen NH, TUGAS, UTS, UAS
                |--------------------------------------------------------------------------
                */

                $statusJenisNilai = $jenisNilais
                    ->map(function (
                        JenisNilai $jenisNilai
                    ) use (
                        $mengajar,
                        $jumlahSiswa
                    ) {
                        $penilaians = $mengajar
                            ->penilaians
                            ->where(
                                'jenis_nilai_id',
                                $jenisNilai->id
                            );

                        if ($penilaians->isEmpty()) {
                            return [
                                'jenis_nilai' =>
                                    $jenisNilai,

                                'jumlah_penilaian' => 0,

                                'jumlah_lengkap' => 0,

                                'status' =>
                                    'belum_dibuat',
                            ];
                        }

                        $jumlahLengkap = $penilaians
                            ->filter(
                                function (
                                    Penilaian $penilaian
                                ) use ($jumlahSiswa) {
                                    if (
                                        $jumlahSiswa === 0
                                    ) {
                                        return false;
                                    }

                                    return $penilaian
                                        ->nilais
                                        ->count()
                                        === $jumlahSiswa;
                                }
                            )
                            ->count();

                        $status = $jumlahLengkap
                            === $penilaians->count()
                                ? 'lengkap'
                                : 'belum_lengkap';

                        return [
                            'jenis_nilai' =>
                                $jenisNilai,

                            'jumlah_penilaian' =>
                                $penilaians->count(),

                            'jumlah_lengkap' =>
                                $jumlahLengkap,

                            'status' =>
                                $status,
                        ];
                    });

                return [
                    'mengajar_id' => $mengajar->id,

                    'guru' => $mengajar->guru,

                    'mata_pelajaran' =>
                        $mengajar->mataPelajaran,

                    'kelas_akademik' =>
                        $mengajar->kelasAkademik,

                    'jumlah_siswa' =>
                        $jumlahSiswa,

                    'jumlah_penilaian' =>
                        $jumlahPenilaian,

                    'penilaian_lengkap' =>
                        $penilaianLengkap,

                    'penilaian_belum_lengkap' =>
                        $penilaianBelumLengkap,

                    'persentase' =>
                        $persentase,

                    'status' => $this
                        ->statusProgres($persentase),

                    'status_jenis_nilai' =>
                        $statusJenisNilai,
                ];
            })
            ->sortBy('persentase')
            ->values();
    }

    /**
     * Pertemuan terbaru pada semester aktif.
     */
    private function getPertemuanTerbaru(
        ?int $semesterId
    ): Collection {
        if (! $semesterId) {
            return collect();
        }

        return Pertemuan::with([
            'mengajar.guru',
            'mengajar.mataPelajaran',
            'mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->withCount('absensis')
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Penilaian terbaru pada semester aktif.
     */
    private function getPenilaianTerbaru(
        ?int $semesterId
    ): Collection {
        if (! $semesterId) {
            return collect();
        }

        return Penilaian::with([
            'jenisNilai',
            'mengajar.guru',
            'mengajar.mataPelajaran',
            'mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->withCount('nilais')
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Status umum progres.
     */
    private function statusProgres(
        float $persentase
    ): string {
        return match (true) {
            $persentase >= 100 => 'lengkap',
            $persentase > 0 => 'proses',
            default => 'belum',
        };
    }
}