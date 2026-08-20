<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $kelasWali = $guru->kelasWaliAktif();

        abort_if(
            ! $kelasWali,
            403,
            'Anda tidak memiliki kelas wali aktif.'
        );

        $semesterId = $request->integer(
            'semester_id'
        );

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->where(
                    'tahun_akademik_id',
                    $kelasWali->tahun_akademik_id
                )
                ->value('id');
        }

        $mataPelajaranId = $request->integer(
            'mata_pelajaran_id'
        );

        $tanggalMulai = $request->date(
            'tanggal_mulai'
        );

        $tanggalSelesai = $request->date(
            'tanggal_selesai'
        );

        $anggotaKelas = AnggotaKelas::with(
            'siswa'
        )
            ->where(
                'kelas_akademik_id',
                $kelasWali->id
            )
            ->get()
            ->sortBy(
                fn (AnggotaKelas $anggota) =>
                    $anggota->siswa?->nama
            )
            ->values();

        $siswaIds = $anggotaKelas
            ->pluck('siswa_id');

        $absensis = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.semester.tahunAkademik',
        ])
            ->whereIn(
                'siswa_id',
                $siswaIds
            )
            ->whereHas(
                'pertemuan.mengajar',
                function ($query) use (
                    $kelasWali,
                    $semesterId,
                    $mataPelajaranId
                ) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasWali->id
                    );

                    if ($semesterId) {
                        $query->where(
                            'semester_id',
                            $semesterId
                        );
                    }

                    if ($mataPelajaranId) {
                        $query->where(
                            'mata_pelajaran_id',
                            $mataPelajaranId
                        );
                    }
                }
            )
            ->when(
                $tanggalMulai,
                fn ($query) => $query->whereHas(
                    'pertemuan',
                    fn ($query) => $query->whereDate(
                        'tanggal',
                        '>=',
                        $tanggalMulai
                    )
                )
            )
            ->when(
                $tanggalSelesai,
                fn ($query) => $query->whereHas(
                    'pertemuan',
                    fn ($query) => $query->whereDate(
                        'tanggal',
                        '<=',
                        $tanggalSelesai
                    )
                )
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Rekap Absensi Per Siswa
        |--------------------------------------------------------------------------
        */

        $rekapAbsensi = $anggotaKelas
            ->map(function (
                AnggotaKelas $anggota
            ) use ($absensis) {
                $absensiSiswa = $absensis->where(
                    'siswa_id',
                    $anggota->siswa_id
                );

                $total = $absensiSiswa->count();

                $hadir = $absensiSiswa
                    ->where('status', 'hadir')
                    ->count();

                $sakit = $absensiSiswa
                    ->where('status', 'sakit')
                    ->count();

                $izin = $absensiSiswa
                    ->where('status', 'izin')
                    ->count();

                $alpa = $absensiSiswa
                    ->where('status', 'alpa')
                    ->count();

                $terlambat = $absensiSiswa
                    ->where('status', 'terlambat')
                    ->count();

                $jumlahKehadiran = $hadir + $terlambat;

                $persentaseKehadiran = $total > 0
                    ? round(
                        ($jumlahKehadiran / $total) * 100,
                        2
                    )
                    : 0;

                return [
                    'siswa' => $anggota->siswa,
                    'total' => $total,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'terlambat' => $terlambat,
                    'persentase_kehadiran' =>
                        $persentaseKehadiran,
                ];
            });

        $semesters = Semester::with(
            'tahunAkademik'
        )
            ->where(
                'tahun_akademik_id',
                $kelasWali->tahun_akademik_id
            )
            ->orderBy('tanggal_mulai')
            ->get();

        $mataPelajarans = MataPelajaran::whereHas(
            'mengajars',
            function ($query) use (
                $kelasWali,
                $semesterId
            ) {
                $query->where(
                    'kelas_akademik_id',
                    $kelasWali->id
                );

                if ($semesterId) {
                    $query->where(
                        'semester_id',
                        $semesterId
                    );
                }
            }
        )
            ->orderBy('nama')
            ->get();

        $kelasWali->load([
            'kelas.jurusan',
            'tahunAkademik',
        ]);

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $rekapAbsensi->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedRekapAbsensi = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $rekapAbsensi->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'wali-kelas.absensi.index',
            compact(
                'kelasWali',
                'rekapAbsensi',
                'paginatedRekapAbsensi',
                'semesters',
                'mataPelajarans',
                'semesterId',
                'mataPelajaranId',
                'tanggalMulai',
                'tanggalSelesai'
            )
        );
    }

    public function show(Siswa $siswa): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $kelasWali = $guru->kelasWaliAktif();

        abort_if(
            ! $kelasWali,
            403,
            'Anda tidak memiliki kelas wali aktif.'
        );

        $this->authorizeSiswaKelas(
            $siswa,
            $kelasWali->id
        );

        $absensis = Absensi::with([
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.semester.tahunAkademik',
        ])
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->whereHas(
                'pertemuan.mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasWali->id
                )
            )
            ->orderByDesc(
                \App\Models\Pertemuan::select(
                    'tanggal'
                )
                    ->whereColumn(
                        'pertemuans.id',
                        'absensis.pertemuan_id'
                    )
            )
            ->get();

        $rekap = [
            'total' => $absensis->count(),

            'hadir' => $absensis
                ->where('status', 'hadir')
                ->count(),

            'sakit' => $absensis
                ->where('status', 'sakit')
                ->count(),

            'izin' => $absensis
                ->where('status', 'izin')
                ->count(),

            'alpa' => $absensis
                ->where('status', 'alpa')
                ->count(),

            'terlambat' => $absensis
                ->where('status', 'terlambat')
                ->count(),
        ];

        $jumlahKehadiran = $rekap['hadir'] + $rekap['terlambat'];

        $rekap['persentase_kehadiran'] =
            $rekap['total'] > 0
                ? round(
                    (
                        $jumlahKehadiran
                        / $rekap['total']
                    ) * 100,
                    2
                )
                : 0;

        return view(
            'wali-kelas.absensi.show',
            compact(
                'kelasWali',
                'siswa',
                'absensis',
                'rekap'
            )
        );
    }

    private function authorizeSiswaKelas(
        Siswa $siswa,
        int $kelasAkademikId
    ): void {
        $isAnggota = AnggotaKelas::where(
            'kelas_akademik_id',
            $kelasAkademikId
        )
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->exists();

        abort_unless(
            $isAnggota,
            403,
            'Anda tidak memiliki akses ke data absensi siswa tersebut.'
        );
    }
}