<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JenisNilai;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NilaiController extends Controller
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

        $jenisNilaiId = $request->integer(
            'jenis_nilai_id'
        );

        $nilais = Nilai::with([
            'penilaian.jenisNilai',
            'penilaian.mengajar.guru',
            'penilaian.mengajar.mataPelajaran',
            'penilaian.mengajar.semester.tahunAkademik',
            'penilaian.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->when(
                $kelasAkademik,
                fn ($query) => $query->whereHas(
                    'penilaian.mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $kelasAkademik->id
                    )
                )
            )
            ->when(
                $semesterId,
                fn ($query) => $query->whereHas(
                    'penilaian.mengajar',
                    fn ($query) => $query->where(
                        'semester_id',
                        $semesterId
                    )
                )
            )
            ->when(
                $mataPelajaranId,
                fn ($query) => $query->whereHas(
                    'penilaian.mengajar',
                    fn ($query) => $query->where(
                        'mata_pelajaran_id',
                        $mataPelajaranId
                    )
                )
            )
            ->when(
                $jenisNilaiId,
                fn ($query) => $query->whereHas(
                    'penilaian',
                    fn ($query) => $query->where(
                        'jenis_nilai_id',
                        $jenisNilaiId
                    )
                )
            )
            ->get();

        $nilaiPerMataPelajaran = $nilais
            ->groupBy(
                fn (Nilai $nilai) =>
                    $nilai
                        ->penilaian
                        ->mengajar
                        ->mataPelajaran
                        ->nama
            )
            ->map(function ($nilaiMapel) {
                return [
                    'mata_pelajaran' => $nilaiMapel
                        ->first()
                        ->penilaian
                        ->mengajar
                        ->mataPelajaran,

                    'guru' => $nilaiMapel
                        ->first()
                        ->penilaian
                        ->mengajar
                        ->guru,

                    'rata_rata' => round(
                        (float) $nilaiMapel->avg('nilai'),
                        2
                    ),

                    'nilai_tertinggi' => $nilaiMapel
                        ->max('nilai'),

                    'nilai_terendah' => $nilaiMapel
                        ->min('nilai'),

                    'nilais' => $nilaiMapel
                        ->sortByDesc(
                            fn (Nilai $nilai) =>
                                $nilai
                                    ->penilaian
                                    ->tanggal
                        )
                        ->values(),
                ];
            });

        $ringkasan = [
            'jumlah_penilaian' => $nilais->count(),

            'rata_rata' => $nilais->isNotEmpty()
                ? round(
                    (float) $nilais->avg('nilai'),
                    2
                )
                : 0,

            'nilai_tertinggi' => $nilais->max(
                'nilai'
            ),

            'nilai_terendah' => $nilais->min(
                'nilai'
            ),
        ];

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

        $jenisNilais = JenisNilai::aktif()
            ->orderBy('nama')
            ->get();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $nilaiPerMataPelajaran->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedNilaiPerMataPelajaran = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $nilaiPerMataPelajaran->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'siswa.nilai.index',
            compact(
                'siswa',
                'kelasAkademik',
                'nilaiPerMataPelajaran',
                'paginatedNilaiPerMataPelajaran',
                'ringkasan',
                'semesters',
                'mataPelajarans',
                'jenisNilais',
                'semesterId',
                'mataPelajaranId',
                'jenisNilaiId'
            )
        );
    }
}