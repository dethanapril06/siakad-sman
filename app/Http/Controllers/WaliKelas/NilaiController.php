<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NilaiController extends Controller
{
    private const KKM = 75;

    public function getBobot(): array
    {
        $bobotMap = \App\Models\JenisNilai::getBobotMap();

        return ! empty($bobotMap) ? $bobotMap : [
            'NH' => 20,
            'TUGAS' => 20,
            'KTR' => 20,
            'UTS' => 20,
            'UAS' => 20,
        ];
    }

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

        $anggotaKelas = AnggotaKelas::with('siswa')
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

        $nilais = Nilai::with([
            'siswa',
            'penilaian.jenisNilai',
            'penilaian.mengajar.guru',
            'penilaian.mengajar.mataPelajaran',
            'penilaian.mengajar.semester.tahunAkademik',
        ])
            ->whereIn(
                'siswa_id',
                $siswaIds
            )
            ->whereHas(
                'penilaian.mengajar',
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
            ->get()
            ->groupBy('siswa_id');

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

        $raportNilai = $anggotaKelas
            ->map(function (
                AnggotaKelas $anggota,
                int $index
            ) use (
                $nilais,
                $mataPelajarans
            ) {
                $nilaiSiswa = $nilais->get(
                    $anggota->siswa_id,
                    collect()
                );

                $nilaiMapel = $mataPelajarans
                    ->mapWithKeys(function (
                        MataPelajaran $mataPelajaran
                    ) use ($nilaiSiswa) {
                        $rekapMapel = $this->rekapMataPelajaran(
                            $nilaiSiswa,
                            $mataPelajaran->id
                        );

                        return [
                            $mataPelajaran->id => $rekapMapel,
                        ];
                    });

                $nilaiAkhir = $nilaiMapel
                    ->pluck('nilai_akhir')
                    ->filter(fn ($nilai) => $nilai !== null);

                $rataRata = $nilaiAkhir->isNotEmpty()
                    ? round((float) $nilaiAkhir->avg(), 2)
                    : null;

                return [
                    'no' => $index + 1,
                    'siswa' => $anggota->siswa,
                    'nilai_mapel' => $nilaiMapel,
                    'rata_rata' => $rataRata,
                    'keterangan' => $rataRata === null
                        ? 'Belum Ada Nilai'
                        : ($rataRata >= self::KKM
                            ? 'Tuntas'
                            : 'Belum Tuntas'),
                ];
            });

        $kelasWali->load([
            'kelas.jurusan',
            'tahunAkademik',
        ]);

        $bobot = $this->getBobot();
        $kkm = self::KKM;

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $raportNilai->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedRaportNilai = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $raportNilai->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'wali-kelas.nilai.index',
            compact(
                'kelasWali',
                'raportNilai',
                'paginatedRaportNilai',
                'semesters',
                'mataPelajarans',
                'semesterId',
                'bobot',
                'kkm',
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

        $semesterId = request()->integer(
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

        $nilais = Nilai::with([
            'penilaian.jenisNilai',
            'penilaian.mengajar.guru',
            'penilaian.mengajar.mataPelajaran',
            'penilaian.mengajar.semester.tahunAkademik',
        ])
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->whereHas(
                'penilaian.mengajar',
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
            ->with([
                'mengajars' => function ($query) use (
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
                },
                'mengajars.guru',
            ])
            ->orderBy('nama')
            ->get();

        $semesters = Semester::with('tahunAkademik')
            ->where(
                'tahun_akademik_id',
                $kelasWali->tahun_akademik_id
            )
            ->orderByDesc('id')
            ->get();

        $raportSiswa = $mataPelajarans
            ->map(function (
                MataPelajaran $mataPelajaran
            ) use ($nilais) {
                $mengajar = $mataPelajaran
                    ->mengajars
                    ->first();

                $rekap = $this->rekapMataPelajaran(
                    $nilais,
                    $mataPelajaran->id
                );

                return [
                    'mata_pelajaran' => $mataPelajaran,
                    'guru' => $mengajar?->guru,
                    'rekap' => $rekap,
                ];
            });

        $bobot = $this->getBobot();
        $kkm = self::KKM;

        return view(
            'wali-kelas.nilai.show',
            compact(
                'kelasWali',
                'siswa',
                'raportSiswa',
                'semesters',
                'semesterId',
                'bobot',
                'kkm',
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
            'Anda tidak memiliki akses ke data nilai siswa tersebut.'
        );
    }

    private function rekapMataPelajaran(
        $nilaiSiswa,
        int $mataPelajaranId
    ): array {
        $nilaiMapel = $nilaiSiswa->filter(
            fn (Nilai $nilai) =>
                $nilai->penilaian?->mengajar?->mata_pelajaran_id
                    === $mataPelajaranId
        );

        $nilaiHarian = $this->averageByJenis(
            $nilaiMapel,
            'NH'
        );

        $nilaiTugas = $this->averageByJenis(
            $nilaiMapel,
            'TUGAS'
        );

        $nilaiKeterampilan = $this->averageByJenis(
            $nilaiMapel,
            'KTR'
        );

        $nilaiUts = $this->averageByJenis(
            $nilaiMapel,
            'UTS'
        );

        $nilaiUas = $this->averageByJenis(
            $nilaiMapel,
            'UAS'
        );

        $nilaiAkhir = $this->calculateWeightedAverage([
            'NH' => $nilaiHarian,
            'TUGAS' => $nilaiTugas,
            'KTR' => $nilaiKeterampilan,
            'UTS' => $nilaiUts,
            'UAS' => $nilaiUas,
        ]);

        return [
            'nilai_harian' => $nilaiHarian,
            'nilai_tugas' => $nilaiTugas,
            'nilai_keterampilan' => $nilaiKeterampilan,
            'nilai_uts' => $nilaiUts,
            'nilai_uas' => $nilaiUas,
            'nilai_akhir' => $nilaiAkhir,
            'kkm' => self::KKM,
            'keterangan' => $nilaiAkhir === null
                ? 'Belum Ada Nilai'
                : ($nilaiAkhir >= self::KKM
                    ? 'Tuntas'
                    : 'Belum Tuntas'),
        ];
    }

    private function averageByJenis(
        $nilaiSiswa,
        string $kode
    ): ?float {
        $nilaiJenis = $nilaiSiswa->filter(
            fn (Nilai $nilai) =>
                $nilai->penilaian?->jenisNilai?->kode === $kode
        );

        if ($nilaiJenis->isEmpty()) {
            return null;
        }

        return round(
            (float) $nilaiJenis->avg('nilai'),
            2
        );
    }

    private function calculateWeightedAverage(
        array $nilai
    ): ?float {
        $bobotMap = $this->getBobot();
        $totalBobot = 0;
        $totalNilai = 0;

        foreach ($bobotMap as $kode => $bobot) {
            if (! isset($nilai[$kode]) || $nilai[$kode] === null) {
                continue;
            }

            $totalBobot += $bobot;
            $totalNilai += $nilai[$kode] * $bobot;
        }

        if ($totalBobot === 0) {
            return null;
        }

        return round(
            $totalNilai / $totalBobot,
            2
        );
    }
}
