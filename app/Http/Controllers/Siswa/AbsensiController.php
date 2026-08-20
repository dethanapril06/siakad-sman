<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\MataPelajaran;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = Auth::user()->siswa;

        abort_if(
            ! $siswa,
            403,
            'Data siswa tidak ditemukan.'
        );

        $anggotaKelasAktif = $siswa->kelasAktif();

        $kelasAkademik = $anggotaKelasAktif
            ?->kelasAkademik;

        $semesterId = $request->integer(
            'semester_id'
        );

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->when(
                    $kelasAkademik,
                    fn ($query) => $query->where(
                        'tahun_akademik_id',
                        $kelasAkademik
                            ->tahun_akademik_id
                    )
                )
                ->value('id');
        }

        $mataPelajaranId = $request->integer(
            'mata_pelajaran_id'
        );

        $status = $request
            ->string('status')
            ->toString();

        $tanggalMulai = $request->date(
            'tanggal_mulai'
        );

        $tanggalSelesai = $request->date(
            'tanggal_selesai'
        );

        $absensis = Absensi::with([
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.semester.tahunAkademik',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->when(
                $kelasAkademik,
                fn ($query) => $query->whereHas(
                    'pertemuan.mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $kelasAkademik->id
                    )
                )
            )
            ->when(
                $semesterId,
                fn ($query) => $query->whereHas(
                    'pertemuan.mengajar',
                    fn ($query) => $query->where(
                        'semester_id',
                        $semesterId
                    )
                )
            )
            ->when(
                $mataPelajaranId,
                fn ($query) => $query->whereHas(
                    'pertemuan.mengajar',
                    fn ($query) => $query->where(
                        'mata_pelajaran_id',
                        $mataPelajaranId
                    )
                )
            )
            ->when(
                $status,
                fn ($query) => $query->where(
                    'status',
                    $status
                )
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
            ->get()
            ->sortByDesc(
                fn (Absensi $absensi) =>
                    $absensi
                        ->pertemuan
                        ->tanggal
                        ->timestamp
            )
            ->values();

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

        $jumlahKehadiran = $rekap['hadir']
            + $rekap['terlambat'];

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

        $rekapPerMataPelajaran = $absensis
            ->groupBy(
                fn (Absensi $absensi) =>
                    $absensi
                        ->pertemuan
                        ->mengajar
                        ->mataPelajaran
                        ->nama
            )
            ->map(function ($absensiMapel) {
                $total = $absensiMapel->count();

                $hadir = $absensiMapel
                    ->where('status', 'hadir')
                    ->count();

                $sakit = $absensiMapel
                    ->where('status', 'sakit')
                    ->count();

                $izin = $absensiMapel
                    ->where('status', 'izin')
                    ->count();

                $alpa = $absensiMapel
                    ->where('status', 'alpa')
                    ->count();

                $terlambat = $absensiMapel
                    ->where('status', 'terlambat')
                    ->count();

                $jumlahKehadiran = $hadir
                    + $terlambat;

                return [
                    'mata_pelajaran' => $absensiMapel
                        ->first()
                        ->pertemuan
                        ->mengajar
                        ->mataPelajaran,

                    'guru' => $absensiMapel
                        ->first()
                        ->pertemuan
                        ->mengajar
                        ->guru,

                    'total' => $total,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'terlambat' => $terlambat,

                    'persentase_kehadiran' =>
                        $total > 0
                            ? round(
                                (
                                    $jumlahKehadiran
                                    / $total
                                ) * 100,
                                2
                            )
                            : 0,
                ];
            });

        $semesters = collect();

        if ($kelasAkademik) {
            $semesters = Semester::with(
                'tahunAkademik'
            )
                ->where(
                    'tahun_akademik_id',
                    $kelasAkademik
                        ->tahun_akademik_id
                )
                ->orderBy('tanggal_mulai')
                ->get();
        }

        $mataPelajarans = collect();

        if ($kelasAkademik) {
            $mataPelajarans = MataPelajaran::whereHas(
                'mengajars',
                function ($query) use (
                    $kelasAkademik,
                    $semesterId
                ) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasAkademik->id
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
        }

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $absensis->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedAbsensis = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $absensis->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'siswa.absensi.index',
            compact(
                'siswa',
                'kelasAkademik',
                'absensis',
                'paginatedAbsensis',
                'rekap',
                'rekapPerMataPelajaran',
                'semesters',
                'mataPelajarans',
                'semesterId',
                'mataPelajaranId',
                'status',
                'tanggalMulai',
                'tanggalSelesai'
            )
        );
    }
}