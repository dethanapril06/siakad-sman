<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Mengajar;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanNilaiController extends Controller
{
    private const KKM = 75;

    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $semesterAktif = Semester::aktif()->first();

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->where('guru_id', $guru->id)
            ->when(
                $request->filled('semester_id'),
                fn ($query) => $query->where(
                    'semester_id',
                    $request->integer('semester_id')
                )
            )
            ->when(
                ! $request->filled('semester_id') && $semesterAktif,
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterAktif->id
                )
            )
            ->orderByDesc('semester_id')
            ->get();

        $selectedMengajarId = $request->integer('mengajar_id')
            ?: $mengajars->first()?->id;

        $selectedMengajar = $selectedMengajarId
            ? $mengajars->firstWhere('id', $selectedMengajarId)
            : null;

        if (
            $selectedMengajarId
            && ! $selectedMengajar
        ) {
            $selectedMengajar = Mengajar::with([
                'semester.tahunAkademik',
                'kelasAkademik.kelas.jurusan',
                'mataPelajaran',
            ])
                ->where('guru_id', $guru->id)
                ->findOrFail($selectedMengajarId);
        }

        $laporanNilai = $this->buildLaporanNilai($selectedMengajar);

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $laporanNilai->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedLaporanNilai = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $laporanNilai->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'guru.laporan-nilai.index',
            [
                'mengajars' => $mengajars,
                'selectedMengajar' => $selectedMengajar,
                'selectedMengajarId' => $selectedMengajarId,
                'laporanNilai' => $laporanNilai,
                'paginatedLaporanNilai' => $paginatedLaporanNilai,
                'bobot' => $this->getBobot(),
                'kkm' => self::KKM,
            ]
        );
    }

    public function cetak(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(! $guru, 403, 'Data guru tidak ditemukan.');

        $mengajarId = $request->integer('mengajar_id');
        $selectedMengajar = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'guru',
        ])
            ->where('guru_id', $guru->id)
            ->findOrFail($mengajarId);

        $laporanNilai = $this->buildLaporanNilai($selectedMengajar);
        $bobot = $this->getBobot();
        $kepalaSekolah = \App\Models\Sekolah::first()?->kepalaSekolah
            ?? \App\Models\Guru::where('status', 'aktif')->first();

        return view('guru.laporan-nilai.cetak', [
            'selectedMengajar' => $selectedMengajar,
            'laporanNilai' => $laporanNilai,
            'bobot' => $bobot,
            'kkm' => self::KKM,
            'guru' => $guru,
            'kepalaSekolah' => $kepalaSekolah,
        ]);
    }

    private function buildLaporanNilai(?Mengajar $selectedMengajar)
    {
        if (! $selectedMengajar) {
            return collect();
        }

        $anggotaKelas = AnggotaKelas::with('siswa')
            ->where('kelas_akademik_id', $selectedMengajar->kelas_akademik_id)
            ->get()
            ->sortBy(fn (AnggotaKelas $anggota) => $anggota->siswa?->nama)
            ->values();

        $nilais = Nilai::with(['penilaian.jenisNilai'])
            ->whereIn('siswa_id', $anggotaKelas->pluck('siswa_id'))
            ->whereHas('penilaian', fn ($query) => $query->where('mengajar_id', $selectedMengajar->id))
            ->get()
            ->groupBy('siswa_id');

        return $anggotaKelas->map(function (AnggotaKelas $anggota, int $index) use ($nilais) {
            $nilaiSiswa = $nilais->get($anggota->siswa_id, collect());

            $rataHarian = $this->averageByJenis($nilaiSiswa, 'NH');
            $rataTugas = $this->averageByJenis($nilaiSiswa, 'TUGAS');
            $rataKeterampilan = $this->averageByJenis($nilaiSiswa, 'KTR');
            $nilaiUts = $this->averageByJenis($nilaiSiswa, 'UTS');
            $nilaiUas = $this->averageByJenis($nilaiSiswa, 'UAS');

            $rataRata = $this->calculateWeightedAverage([
                'NH' => $rataHarian,
                'TUGAS' => $rataTugas,
                'KTR' => $rataKeterampilan,
                'UTS' => $nilaiUts,
                'UAS' => $nilaiUas,
            ]);

            return [
                'no' => $index + 1,
                'siswa' => $anggota->siswa,
                'nilai_harian' => $rataHarian,
                'nilai_tugas' => $rataTugas,
                'nilai_keterampilan' => $rataKeterampilan,
                'nilai_uts' => $nilaiUts,
                'nilai_uas' => $nilaiUas,
                'rata_rata' => $rataRata,
                'kkm' => self::KKM,
                'keterangan' => $rataRata >= self::KKM ? 'Tuntas' : 'Belum Tuntas',
            ];
        });
    }

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

    private function averageByJenis($nilaiSiswa, string $kode): ?float
    {
        $nilaiJenis = $nilaiSiswa->filter(
            fn (Nilai $nilai) => $nilai->penilaian?->jenisNilai?->kode === $kode
        );

        if ($nilaiJenis->isEmpty()) {
            return null;
        }

        return round((float) $nilaiJenis->avg('nilai'), 2);
    }

    private function calculateWeightedAverage(array $nilai): float
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
            return 0;
        }

        return round($totalNilai / $totalBobot, 2);
    }
}
