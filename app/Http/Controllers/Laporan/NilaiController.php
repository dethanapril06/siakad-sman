<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
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
        $tahunAkademikId = $request->integer(
            'tahun_akademik_id'
        );

        if (! $tahunAkademikId) {
            $tahunAkademikId = TahunAkademik::aktif()
                ->value('id');
        }

        $semesterId = $request->integer(
            'semester_id'
        );

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->when(
                    $tahunAkademikId,
                    fn ($query) => $query->where(
                        'tahun_akademik_id',
                        $tahunAkademikId
                    )
                )
                ->value('id');
        }

        $kelasAkademikId = $request->integer(
            'kelas_akademik_id'
        );

        $mataPelajaranId = $request->integer(
            'mata_pelajaran_id'
        );

        $guruId = $request->integer(
            'guru_id'
        );

        /*
        |--------------------------------------------------------------------------
        | Query Kelas Akademik
        |--------------------------------------------------------------------------
        */

        $kelasLaporan = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])
            ->when(
                $tahunAkademikId,
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                )
            )
            ->when(
                $kelasAkademikId,
                fn ($query) => $query->where(
                    'id',
                    $kelasAkademikId
                )
            )
            ->whereHas(
                'mengajars',
                function ($query) use (
                    $semesterId,
                    $mataPelajaranId,
                    $guruId
                ) {
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

                    if ($guruId) {
                        $query->where(
                            'guru_id',
                            $guruId
                        );
                    }
                }
            )
            ->orderBy('kelas_id')
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Rekap Raport Nilai
        |--------------------------------------------------------------------------
        */

        $laporanNilai = $kelasLaporan
            ->map(function (
                KelasAkademik $kelasAkademik
            ) use (
                $semesterId,
                $mataPelajaranId,
                $guruId
            ) {
                $anggotaKelas = AnggotaKelas::with(
                    'siswa'
                )
                    ->where(
                        'kelas_akademik_id',
                        $kelasAkademik->id
                    )
                    ->get()
                    ->sortBy(
                        fn (AnggotaKelas $anggota) =>
                            $anggota->siswa?->nama
                    )
                    ->values();

                $siswaIds = $anggotaKelas
                    ->pluck('siswa_id');

                $mataPelajaranLaporan = MataPelajaran::whereHas(
                    'mengajars',
                    function ($query) use (
                        $kelasAkademik,
                        $semesterId,
                        $mataPelajaranId,
                        $guruId
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

                        if ($mataPelajaranId) {
                            $query->where(
                                'mata_pelajaran_id',
                                $mataPelajaranId
                            );
                        }

                        if ($guruId) {
                            $query->where(
                                'guru_id',
                                $guruId
                            );
                        }
                    }
                )
                    ->orderBy('nama')
                    ->get();

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
                            $kelasAkademik,
                            $semesterId,
                            $mataPelajaranId,
                            $guruId
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

                            if ($mataPelajaranId) {
                                $query->where(
                                    'mata_pelajaran_id',
                                    $mataPelajaranId
                                );
                            }

                            if ($guruId) {
                                $query->where(
                                    'guru_id',
                                    $guruId
                                );
                            }
                        }
                    )
                    ->get()
                    ->groupBy('siswa_id');

                $raportNilai = $anggotaKelas
                    ->map(function (
                        AnggotaKelas $anggota,
                        int $index
                    ) use (
                        $nilais,
                        $mataPelajaranLaporan
                    ) {
                        $nilaiSiswa = $nilais->get(
                            $anggota->siswa_id,
                            collect()
                        );

                        $nilaiMapel = $mataPelajaranLaporan
                            ->mapWithKeys(function (
                                MataPelajaran $mataPelajaran
                            ) use ($nilaiSiswa) {
                                return [
                                    $mataPelajaran->id =>
                                        $this->rekapMataPelajaran(
                                            $nilaiSiswa,
                                            $mataPelajaran->id
                                        ),
                                ];
                            });

                        $nilaiAkhir = $nilaiMapel
                            ->pluck('nilai_akhir')
                            ->filter(
                                fn ($nilai) =>
                                    $nilai !== null
                            );

                        $rataRata = $nilaiAkhir->isNotEmpty()
                            ? round(
                                (float) $nilaiAkhir->avg(),
                                2
                            )
                            : null;

                        return [
                            'no' => $index + 1,

                            'siswa' =>
                                $anggota->siswa,

                            'nilai_mapel' =>
                                $nilaiMapel,

                            'rata_rata' =>
                                $rataRata,

                            'keterangan' => $rataRata === null
                                ? 'Belum Ada Nilai'
                                : ($rataRata >= self::KKM
                                    ? 'Tuntas'
                                    : 'Belum Tuntas'),
                        ];
                    });

                $nilaiAkhirKelas = $raportNilai
                    ->pluck('rata_rata')
                    ->filter(
                        fn ($nilai) =>
                            $nilai !== null
                    );

                return [
                    'kelas_akademik' => $kelasAkademik,

                    'jumlah_siswa' =>
                        $anggotaKelas->count(),

                    'jumlah_mapel' =>
                        $mataPelajaranLaporan->count(),

                    'rata_rata_kelas' =>
                        $nilaiAkhirKelas->isNotEmpty()
                            ? round(
                                (float) $nilaiAkhirKelas->avg(),
                                2
                            )
                            : null,

                    'mata_pelajarans' =>
                        $mataPelajaranLaporan,

                    'raport_nilai' =>
                        $raportNilai,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Data Filter
        |--------------------------------------------------------------------------
        */

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        $semesters = Semester::with(
            'tahunAkademik'
        )
            ->when(
                $tahunAkademikId,
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                )
            )
            ->orderBy('tanggal_mulai')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])
            ->when(
                $tahunAkademikId,
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                )
            )
            ->get();

        $mataPelajarans = MataPelajaran::whereHas(
            'mengajars',
            function ($query) use (
                $semesterId,
                $kelasAkademikId
            ) {
                if ($semesterId) {
                    $query->where(
                        'semester_id',
                        $semesterId
                    );
                }

                if ($kelasAkademikId) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasAkademikId
                    );
                }
            }
        )
            ->orderBy('nama')
            ->get();

        $gurus = Guru::whereHas(
            'mengajars',
            function ($query) use (
                $semesterId,
                $kelasAkademikId,
                $mataPelajaranId
            ) {
                if ($semesterId) {
                    $query->where(
                        'semester_id',
                        $semesterId
                    );
                }

                if ($kelasAkademikId) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasAkademikId
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
            ->orderBy('nama')
            ->get();

        $bobot = $this->getBobot();
        $kkm = self::KKM;

        return view(
            'laporan.nilai.index',
            compact(
                'laporanNilai',
                'kelasLaporan',
                'tahunAkademiks',
                'semesters',
                'kelasAkademiks',
                'mataPelajarans',
                'gurus',
                'tahunAkademikId',
                'semesterId',
                'kelasAkademikId',
                'mataPelajaranId',
                'guruId',
                'bobot',
                'kkm'
            )
        );
    }

    public function cetak(Request $request): View
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $kelasAkademikId = $request->integer('kelas_akademik_id');
        $mataPelajaranId = $request->integer('mata_pelajaran_id');
        $guruId = $request->integer('guru_id');

        $kelasLaporan = KelasAkademik::with(['kelas.jurusan', 'tahunAkademik', 'guru'])
            ->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))
            ->when($kelasAkademikId, fn ($q) => $q->where('id', $kelasAkademikId))
            ->whereHas('mengajars', function ($query) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $query->where('semester_id', $semesterId);
                if ($mataPelajaranId) $query->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $query->where('guru_id', $guruId);
            })
            ->orderBy('kelas_id')
            ->get();

        $laporanNilai = $kelasLaporan->map(function (KelasAkademik $kelasAkademik) use ($semesterId, $mataPelajaranId, $guruId) {
            $anggotaKelas = AnggotaKelas::with('siswa')->where('kelas_akademik_id', $kelasAkademik->id)->get()->sortBy(fn ($a) => $a->siswa?->nama)->values();
            $siswaIds = $anggotaKelas->pluck('siswa_id');

            $mataPelajaranLaporan = MataPelajaran::whereHas('mengajars', function ($q) use ($kelasAkademik, $semesterId, $mataPelajaranId, $guruId) {
                $q->where('kelas_akademik_id', $kelasAkademik->id);
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $q->where('guru_id', $guruId);
            })->orderBy('nama')->get();

            $nilais = Nilai::with(['siswa', 'penilaian.jenisNilai', 'penilaian.mengajar.guru', 'penilaian.mengajar.mataPelajaran'])
                ->whereIn('siswa_id', $siswaIds)
                ->whereHas('penilaian.mengajar', function ($q) use ($kelasAkademik, $semesterId, $mataPelajaranId, $guruId) {
                    $q->where('kelas_akademik_id', $kelasAkademik->id);
                    if ($semesterId) $q->where('semester_id', $semesterId);
                    if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                    if ($guruId) $q->where('guru_id', $guruId);
                })->get()->groupBy('siswa_id');

            $raportNilai = $anggotaKelas->map(function (AnggotaKelas $anggota, int $index) use ($nilais, $mataPelajaranLaporan) {
                $nilaiSiswa = $nilais->get($anggota->siswa_id, collect());
                $nilaiMapel = $mataPelajaranLaporan->mapWithKeys(function (MataPelajaran $mapel) use ($nilaiSiswa) {
                    return [$mapel->id => $this->rekapMataPelajaran($nilaiSiswa, $mapel->id)];
                });
                $nilaiAkhir = $nilaiMapel->pluck('nilai_akhir')->filter(fn ($v) => $v !== null);
                $rataRata = $nilaiAkhir->isNotEmpty() ? round((float) $nilaiAkhir->avg(), 2) : null;

                return [
                    'no' => $index + 1,
                    'siswa' => $anggota->siswa,
                    'nilai_mapel' => $nilaiMapel,
                    'rata_rata' => $rataRata,
                    'keterangan' => $rataRata === null ? 'Belum Ada Nilai' : ($rataRata >= self::KKM ? 'Tuntas' : 'Belum Tuntas'),
                ];
            });

            return [
                'kelas_akademik' => $kelasAkademik,
                'mata_pelajarans' => $mataPelajaranLaporan,
                'raport_nilai' => $raportNilai,
            ];
        });

        $semester = Semester::with('tahunAkademik')->find($semesterId);
        $kepalaSekolah = Guru::whereHas('user.role', fn ($q) => $q->where('name', 'kepala_sekolah'))->where('status', 'aktif')->first() ?? Guru::where('status', 'aktif')->first();

        return view('laporan.nilai.cetak', compact('laporanNilai', 'semester', 'kepalaSekolah'));
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $kelasAkademikId = $request->integer('kelas_akademik_id');
        $mataPelajaranId = $request->integer('mata_pelajaran_id');
        $guruId = $request->integer('guru_id');

        $kelasLaporan = KelasAkademik::with(['kelas.jurusan', 'tahunAkademik'])
            ->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))
            ->when($kelasAkademikId, fn ($q) => $q->where('id', $kelasAkademikId))
            ->whereHas('mengajars', function ($query) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $query->where('semester_id', $semesterId);
                if ($mataPelajaranId) $query->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $query->where('guru_id', $guruId);
            })
            ->orderBy('kelas_id')
            ->get();

        $filename = 'Laporan_Nilai_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($kelasLaporan, $semesterId, $mataPelajaranId, $guruId) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['No', 'Kelas', 'NISN', 'NIS', 'Nama Siswa', 'Mata Pelajaran', 'Nilai Harian', 'Tugas', 'UTS', 'UAS', 'Nilai Akhir', 'Status']);

            $no = 1;
            foreach ($kelasLaporan as $kelasAkademik) {
                $anggotaKelas = AnggotaKelas::with('siswa')->where('kelas_akademik_id', $kelasAkademik->id)->get()->sortBy(fn ($a) => $a->siswa?->nama)->values();
                $siswaIds = $anggotaKelas->pluck('siswa_id');

                $mataPelajaranLaporan = MataPelajaran::whereHas('mengajars', function ($q) use ($kelasAkademik, $semesterId, $mataPelajaranId, $guruId) {
                    $q->where('kelas_akademik_id', $kelasAkademik->id);
                    if ($semesterId) $q->where('semester_id', $semesterId);
                    if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                    if ($guruId) $q->where('guru_id', $guruId);
                })->orderBy('nama')->get();

                $nilais = Nilai::with(['siswa', 'penilaian.jenisNilai', 'penilaian.mengajar'])
                    ->whereIn('siswa_id', $siswaIds)
                    ->whereHas('penilaian.mengajar', function ($q) use ($kelasAkademik, $semesterId, $mataPelajaranId, $guruId) {
                        $q->where('kelas_akademik_id', $kelasAkademik->id);
                        if ($semesterId) $q->where('semester_id', $semesterId);
                        if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                        if ($guruId) $q->where('guru_id', $guruId);
                    })->get()->groupBy('siswa_id');

                foreach ($anggotaKelas as $anggota) {
                    $siswa = $anggota->siswa;
                    $nilaiSiswa = $nilais->get($siswa->id, collect());

                    foreach ($mataPelajaranLaporan as $mapel) {
                        $rekap = $this->rekapMataPelajaran($nilaiSiswa, $mapel->id);
                        fputcsv($handle, [
                            $no++,
                            $kelasAkademik->nama_lengkap,
                            $siswa->nisn ?? '-',
                            $siswa->nis ?? '-',
                            $siswa->nama,
                            $mapel->nama,
                            $rekap['nilai_harian'] ?? '-',
                            $rekap['nilai_tugas'] ?? '-',
                            $rekap['nilai_uts'] ?? '-',
                            $rekap['nilai_uas'] ?? '-',
                            $rekap['nilai_akhir'] ?? '-',
                            $rekap['keterangan'] ?? '-',
                        ]);
                    }
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
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
