<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\CatatanWaliKelas;
use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Support\Collection;

class RaporService
{
    public const KKM = 75;

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

    /**
     * Mengambil data rapor lengkap untuk 1 siswa pada semester tertentu.
     */
    public function getRaporSiswa(
        Siswa $siswa,
        ?int $semesterId = null,
        ?int $kelasAkademikId = null
    ): array {
        // Tentukan semester
        $semester = $semesterId
            ? Semester::with('tahunAkademik')->find($semesterId)
            : Semester::aktif()->with('tahunAkademik')->first();

        // Tentukan kelas akademik siswa
        $anggotaKelas = AnggotaKelas::with('kelasAkademik.kelas.jurusan', 'kelasAkademik.waliKelas', 'kelasAkademik.tahunAkademik')
            ->where('siswa_id', $siswa->id)
            ->when($kelasAkademikId, fn ($q) => $q->where('kelas_akademik_id', $kelasAkademikId))
            ->when(! $kelasAkademikId && $semester, fn ($q) => $q->whereHas('kelasAkademik', fn ($sq) => $sq->where('tahun_akademik_id', $semester->tahun_akademik_id)))
            ->latest('id')
            ->first();

        $kelasAkademik = $anggotaKelas?->kelasAkademik;

        if (! $kelasAkademik || ! $semester) {
            return [];
        }

        // Ambil semua mata pelajaran pada kelas ini
        $mataPelajarans = MataPelajaran::whereHas(
            'mengajars',
            fn ($query) => $query
                ->where('kelas_akademik_id', $kelasAkademik->id)
                ->where('semester_id', $semester->id)
        )
            ->orderBy('nama')
            ->get();

        // Ambil semua nilai siswa pada semester & kelas ini
        $nilais = Nilai::with(['penilaian.jenisNilai', 'penilaian.mengajar'])
            ->where('siswa_id', $siswa->id)
            ->whereHas(
                'penilaian.mengajar',
                fn ($query) => $query
                    ->where('kelas_akademik_id', $kelasAkademik->id)
                    ->where('semester_id', $semester->id)
            )
            ->get();

        // Rekap nilai per mapel
        $nilaiMapel = $mataPelajarans->map(function (MataPelajaran $mapel) use ($nilais) {
            $nilaiSiswaMapel = $nilais->filter(
                fn (Nilai $n) => $n->penilaian?->mengajar?->mata_pelajaran_id === $mapel->id
            );

            $nilaiHarian = $this->averageByJenis($nilaiSiswaMapel, 'NH');
            $nilaiTugas = $this->averageByJenis($nilaiSiswaMapel, 'TUGAS');
            $nilaiKeterampilan = $this->averageByJenis($nilaiSiswaMapel, 'KTR');
            $nilaiUts = $this->averageByJenis($nilaiSiswaMapel, 'UTS');
            $nilaiUas = $this->averageByJenis($nilaiSiswaMapel, 'UAS');

            $nilaiAkhir = $this->calculateWeightedAverage([
                'NH' => $nilaiHarian,
                'TUGAS' => $nilaiTugas,
                'KTR' => $nilaiKeterampilan,
                'UTS' => $nilaiUts,
                'UAS' => $nilaiUas,
            ]);

            $predikat = $this->getPredikat($nilaiAkhir);
            $capaian = $this->getCapaianKompetensi($mapel->nama, $nilaiAkhir, $predikat);

            return [
                'mata_pelajaran' => $mapel,
                'kkm' => self::KKM,
                'nilai_harian' => $nilaiHarian,
                'nilai_tugas' => $nilaiTugas,
                'nilai_keterampilan' => $nilaiKeterampilan,
                'nilai_uts' => $nilaiUts,
                'nilai_uas' => $nilaiUas,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
                'keterangan' => $nilaiAkhir === null
                    ? 'Belum Tuntas'
                    : ($nilaiAkhir >= self::KKM ? 'Tuntas' : 'Belum Tuntas'),
                'capaian_kompetensi' => $capaian,
            ];
        });

        $nilaiAkhirList = $nilaiMapel->pluck('nilai_akhir')->filter(fn ($v) => $v !== null);
        $rataRata = $nilaiAkhirList->isNotEmpty() ? round((float) $nilaiAkhirList->avg(), 2) : null;
        $totalNilai = $nilaiAkhirList->isNotEmpty() ? round((float) $nilaiAkhirList->sum(), 2) : 0;

        // Hitung peringkat kelas
        $ledgerKelas = $this->getLedgerKelas($kelasAkademik->id, $semester->id);
        $peringkat = null;
        $totalSiswa = count($ledgerKelas);
        foreach ($ledgerKelas as $rank => $item) {
            if ($item['siswa']->id === $siswa->id) {
                $peringkat = $rank + 1;
                break;
            }
        }

        // Rekap presensi semester ini
        $presensi = $this->getPresensiSiswa($siswa->id, $kelasAkademik->id, $semester->id);

        // Catatan wali kelas
        $catatanWali = CatatanWaliKelas::where('siswa_id', $siswa->id)
            ->where('semester_id', $semester->id)
            ->where('kelas_akademik_id', $kelasAkademik->id)
            ->first();

        return [
            'siswa' => $siswa,
            'kelasAkademik' => $kelasAkademik,
            'semester' => $semester,
            'mataPelajarans' => $mataPelajarans,
            'nilaiMapel' => $nilaiMapel,
            'rataRata' => $rataRata,
            'totalNilai' => $totalNilai,
            'peringkat' => $peringkat,
            'totalSiswa' => $totalSiswa,
            'presensi' => $presensi,
            'catatanWali' => $catatanWali,
            'kepalaSekolah' => $this->getKepalaSekolah(),
        ];
    }

    /**
     * Mengambil ledger nilai lengkap untuk 1 rombel/kelas akademik.
     */
    public function getLedgerKelas(int $kelasAkademikId, int $semesterId): array
    {
        $kelasAkademik = KelasAkademik::with('kelas.jurusan', 'tahunAkademik', 'waliKelas')->findOrFail($kelasAkademikId);
        $semester = Semester::with('tahunAkademik')->findOrFail($semesterId);

        $anggotaKelas = AnggotaKelas::with('siswa')
            ->where('kelas_akademik_id', $kelasAkademikId)
            ->get()
            ->sortBy(fn (AnggotaKelas $a) => $a->siswa?->nama)
            ->values();

        $siswaIds = $anggotaKelas->pluck('siswa_id');

        $mataPelajarans = MataPelajaran::whereHas(
            'mengajars',
            fn ($query) => $query
                ->where('kelas_akademik_id', $kelasAkademikId)
                ->where('semester_id', $semesterId)
        )
            ->orderBy('nama')
            ->get();

        $nilais = Nilai::with(['penilaian.jenisNilai', 'penilaian.mengajar'])
            ->whereIn('siswa_id', $siswaIds)
            ->whereHas(
                'penilaian.mengajar',
                fn ($query) => $query
                    ->where('kelas_akademik_id', $kelasAkademikId)
                    ->where('semester_id', $semesterId)
            )
            ->get()
            ->groupBy('siswa_id');

        $rows = $anggotaKelas->map(function (AnggotaKelas $anggota) use ($nilais, $mataPelajarans, $kelasAkademikId, $semesterId) {
            $siswa = $anggota->siswa;
            $nilaiSiswa = $nilais->get($siswa->id, collect());

            $nilaiMapel = $mataPelajarans->mapWithKeys(function (MataPelajaran $mapel) use ($nilaiSiswa) {
                $nilaiSiswaMapel = $nilaiSiswa->filter(
                    fn (Nilai $n) => $n->penilaian?->mengajar?->mata_pelajaran_id === $mapel->id
                );

                $nilaiHarian = $this->averageByJenis($nilaiSiswaMapel, 'NH');
                $nilaiTugas = $this->averageByJenis($nilaiSiswaMapel, 'TUGAS');
                $nilaiKeterampilan = $this->averageByJenis($nilaiSiswaMapel, 'KTR');
                $nilaiUts = $this->averageByJenis($nilaiSiswaMapel, 'UTS');
                $nilaiUas = $this->averageByJenis($nilaiSiswaMapel, 'UAS');

                $nilaiAkhir = $this->calculateWeightedAverage([
                    'NH' => $nilaiHarian,
                    'TUGAS' => $nilaiTugas,
                    'KTR' => $nilaiKeterampilan,
                    'UTS' => $nilaiUts,
                    'UAS' => $nilaiUas,
                ]);

                return [
                    $mapel->id => [
                        'nilai_akhir' => $nilaiAkhir,
                        'predikat' => $this->getPredikat($nilaiAkhir),
                    ],
                ];
            });

            $nilaiAkhirList = $nilaiMapel->pluck('nilai_akhir')->filter(fn ($v) => $v !== null);
            $rataRata = $nilaiAkhirList->isNotEmpty() ? round((float) $nilaiAkhirList->avg(), 2) : 0;
            $totalNilai = $nilaiAkhirList->isNotEmpty() ? round((float) $nilaiAkhirList->sum(), 2) : 0;

            $presensi = $this->getPresensiSiswa($siswa->id, $kelasAkademikId, $semesterId);

            return [
                'siswa' => $siswa,
                'nilai_mapel' => $nilaiMapel,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'presensi' => $presensi,
            ];
        })
            ->sortByDesc('rata_rata')
            ->values()
            ->all();

        return $rows;
    }

    /**
     * Rekap presensi siswa semester aktif.
     */
    public function getPresensiSiswa(int $siswaId, int $kelasAkademikId, int $semesterId): array
    {
        $absensis = Absensi::where('siswa_id', $siswaId)
            ->whereHas(
                'pertemuan.mengajar',
                fn ($query) => $query
                    ->where('kelas_akademik_id', $kelasAkademikId)
                    ->where('semester_id', $semesterId)
            )
            ->get(['status']);

        $hadir = $absensis->where('status', 'hadir')->count();
        $terlambat = $absensis->where('status', 'terlambat')->count();
        $sakit = $absensis->where('status', 'sakit')->count();
        $izin = $absensis->where('status', 'izin')->count();
        $alpa = $absensis->where('status', 'alpa')->count();

        return [
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'total' => $absensis->count(),
        ];
    }

    private function averageByJenis(Collection $nilaiSiswa, string $kode): ?float
    {
        $nilaiJenis = $nilaiSiswa->filter(
            fn (Nilai $nilai) => $nilai->penilaian?->jenisNilai?->kode === $kode
        );

        if ($nilaiJenis->isEmpty()) {
            return null;
        }

        return round((float) $nilaiJenis->avg('nilai'), 2);
    }

    private function calculateWeightedAverage(array $nilai): ?float
    {
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

        return round($totalNilai / $totalBobot, 2);
    }

    public function getPredikat(?float $nilai): string
    {
        if ($nilai === null) {
            return '-';
        }

        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 75 => 'C',
            default => 'D',
        };
    }

    public function getCapaianKompetensi(string $mapelNama, ?float $nilai, string $predikat): string
    {
        if ($nilai === null) {
            return 'Belum ada penilaian yang tercatat pada semester ini.';
        }

        return match ($predikat) {
            'A' => "Menunjukkan penguasaan yang sangat baik dalam seluruh kompetensi dan capaian pembelajaran mata pelajaran {$mapelNama}.",
            'B' => "Menunjukkan penguasaan yang baik dalam capaian pembelajaran mata pelajaran {$mapelNama}, pertahankan dan tingkatkan konsistensi belajar.",
            'C' => "Menunjukkan penguasaan yang cukup dalam mencapai kriteria minimal mata pelajaran {$mapelNama}, perlu peningkatan pemahaman materi lanjutan.",
            default => "Perlu bimbingan dan pendampingan intensif dalam menguasai kompetensi dasar mata pelajaran {$mapelNama}.",
        };
    }

    public function getKepalaSekolah(): ?Guru
    {
        return Guru::whereHas('user.role', fn ($q) => $q->where('name', 'kepala_sekolah'))
            ->where('status', 'aktif')
            ->first() ?? Guru::where('status', 'aktif')->first();
    }
}
