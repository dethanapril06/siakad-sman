<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Jadwal;
use App\Models\Mengajar;
use App\Models\Nilai;
use App\Models\Semester;
use App\Services\DashboardMonitoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function pegawaiTu(
        DashboardMonitoringService $monitoringService
    ): View {
        return view(
            'dashboard.pegawai-tu',
            $monitoringService->getDashboardData()
        );
    }

    public function guru(): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $kelasWali = $guru->kelasWaliAktif();

        if ($kelasWali) {
            $kelasWali->load([
                'kelas.jurusan',
                'tahunAkademik',
                'anggotaKelas.siswa',
                'mengajars.mataPelajaran',
            ]);
        }

        $semesterAktif = Semester::aktif()
            ->with('tahunAkademik')
            ->first();

        $mengajars = Mengajar::with([
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->where('guru_id', $guru->id)
            ->when(
                $semesterAktif,
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterAktif->id
                )
            )
            ->get();

        $mengajarIds = $mengajars->pluck('id');

        $jumlahKelas = $mengajars
            ->pluck('kelas_akademik_id')
            ->unique()
            ->count();

        $kelasAkademikIds = $mengajars
            ->pluck('kelas_akademik_id')
            ->unique();

        $totalSiswa = AnggotaKelas::whereIn(
            'kelas_akademik_id',
            $kelasAkademikIds
        )
            ->distinct()
            ->count('siswa_id');


        $jadwals = Jadwal::whereIn(
            'mengajar_id',
            $mengajarIds
        )->get();

        $totalMenitMengajar = $jadwals->sum(
            function (Jadwal $jadwal) {
                $jamMulai = strtotime(
                    $jadwal->jam_mulai
                );

                $jamSelesai = strtotime(
                    $jadwal->jam_selesai
                );

                return max(
                    0,
                    ($jamSelesai - $jamMulai) / 60
                );
            }
        );

        $jumlahJamMengajar = round(
            $totalMenitMengajar / 60,
            2
        );

        $totalAbsensi = Absensi::whereHas(
            'pertemuan',
            fn ($query) => $query->whereIn(
                'mengajar_id',
                $mengajarIds
            )
        )->count();

        $totalHadir = Absensi::whereHas('pertemuan',
            fn ($query) => $query->whereIn(
                'mengajar_id',
                $mengajarIds
            )
        )
            ->whereIn('status', [
                'hadir',
                'terlambat',
            ])
            ->count();

        $persentaseKehadiran = $totalAbsensi > 0
            ? round(
                ($totalHadir / $totalAbsensi) * 100,
                2
            )
            : 0;

        $hariIni = $this->hariIndonesia();

        $jadwalHariIni = Jadwal::with([
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereIn(
                'mengajar_id',
                $mengajarIds
            )
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();


        $rataRataNilaiPerKelas = $mengajars
            ->map(function (Mengajar $mengajar) {
                $rataRata = Nilai::whereHas(
                    'penilaian',
                    fn ($query) => $query->where(
                        'mengajar_id',
                        $mengajar->id
                    )
                )
                    ->avg('nilai');

                return [
                    'mengajar_id' => $mengajar->id,

                    'kelas' => $mengajar
                        ->kelasAkademik
                        ->nama_lengkap,

                    'mata_pelajaran' => $mengajar
                        ->mataPelajaran
                        ->nama,

                    'rata_rata' => $rataRata !== null
                        ? round((float) $rataRata, 2)
                        : 0,
                ];
            });

        $waliJumlahSiswa = $kelasWali
            ? $kelasWali->anggotaKelas->count()
            : 0;

        $waliJumlahMapel = $kelasWali
            ? $kelasWali->mengajars->pluck('mata_pelajaran_id')->unique()->count()
            : 0;

        $waliSiswaIds = $kelasWali
            ? $kelasWali->anggotaKelas->pluck('siswa_id')
            : collect();

        $waliTotalAbsensi = $kelasWali
            ? Absensi::whereIn('siswa_id', $waliSiswaIds)
                ->whereHas(
                    'pertemuan.mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $kelasWali->id
                    )
                )
                ->count()
            : 0;

        $waliTotalHadir = $kelasWali
            ? Absensi::whereIn('siswa_id', $waliSiswaIds)
                ->whereHas(
                    'pertemuan.mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $kelasWali->id
                    )
                )
                ->whereIn('status', [
                    'hadir',
                    'terlambat',
                ])
                ->count()
            : 0;

        $waliPersentaseKehadiran = $waliTotalAbsensi > 0
            ? round(($waliTotalHadir / $waliTotalAbsensi) * 100, 2)
            : 0;

        $waliRataRataNilai = $kelasWali
            ? Nilai::whereIn('siswa_id', $waliSiswaIds)
                ->whereHas(
                    'penilaian.mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $kelasWali->id
                    )
                )
                ->avg('nilai')
            : null;

        $waliRataRataNilai = $waliRataRataNilai !== null
            ? round((float) $waliRataRataNilai, 2)
            : 0;

        return view(
            'dashboard.guru',
            compact(
                'guru',
                'semesterAktif',
                'jumlahKelas',
                'totalSiswa',
                'jumlahJamMengajar',
                'persentaseKehadiran',
                'jadwalHariIni',
                'rataRataNilaiPerKelas',
                'kelasWali',
                'waliJumlahSiswa',
                'waliJumlahMapel',
                'waliPersentaseKehadiran',
                'waliRataRataNilai',
            )
        );
    }

    public function siswa(): View
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

        $semesterAktif = Semester::aktif()
            ->with('tahunAkademik')
            ->first();

        $nilaiQuery = Nilai::where(
            'siswa_id',
            $siswa->id
        );

        if ($semesterAktif) {
            $nilaiQuery->whereHas(
                'penilaian.mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterAktif->id
                )
            );
        }

        if ($kelasAkademik) {
            $nilaiQuery->whereHas(
                'penilaian.mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasAkademik->id
                )
            );
        }

        $rataRataNilai = (clone $nilaiQuery)->avg(
            'nilai'
        );

        $jumlahNilai = (clone $nilaiQuery)->count();

        $absensiQuery = Absensi::where(
            'siswa_id',
            $siswa->id
        );

        if ($semesterAktif) {
            $absensiQuery->whereHas(
                'pertemuan.mengajar',
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterAktif->id
                )
            );
        }

        if ($kelasAkademik) {
            $absensiQuery->whereHas(
                'pertemuan.mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasAkademik->id
                )
            );
        }

        $absensis = $absensiQuery->get();

        $totalAbsensi = $absensis->count();

        $jumlahHadir = $absensis
            ->whereIn('status', [
                'hadir',
                'terlambat',
            ])
            ->count();

        $jumlahTerlambat = $absensis
            ->where('status', 'terlambat')
            ->count();

        $persentaseKehadiran = $totalAbsensi > 0
            ? round(
                ($jumlahHadir / $totalAbsensi) * 100,
                2
            )
            : 0;

        $hariIni = $this->hariIndonesia();

        $jadwalHariIni = collect();

        if ($kelasAkademik && $semesterAktif) {
            $jadwalHariIni = Jadwal::with([
                'mengajar.guru',
                'mengajar.mataPelajaran',
                'ruangan',
            ])
                ->whereHas(
                    'mengajar',
                    fn ($query) => $query
                        ->where(
                            'kelas_akademik_id',
                            $kelasAkademik->id
                        )
                        ->where(
                            'semester_id',
                            $semesterAktif->id
                        )
                )
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
        }

        $nilaiTerbaru = Nilai::with([
            'penilaian.jenisNilai',
            'penilaian.mengajar.mataPelajaran',
        ])
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->when(
                $semesterAktif,
                fn ($query) => $query->whereHas(
                    'penilaian.mengajar',
                    fn ($query) => $query->where(
                        'semester_id',
                        $semesterAktif->id
                    )
                )
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
            ->latest()
            ->limit(5)
            ->get();

        return view(
            'dashboard.siswa',
            compact(
                'siswa',
                'anggotaKelasAktif',
                'kelasAkademik',
                'semesterAktif',
                'rataRataNilai',
                'jumlahNilai',
                'totalAbsensi',
                'persentaseKehadiran',
                'jumlahTerlambat',
                'jadwalHariIni',
                'nilaiTerbaru'
            )
        );
    }

    public function kepalaSekolah(
        DashboardMonitoringService $monitoringService
    ): View {
        $dashboardData = $monitoringService->getDashboardData();
        $semesterAktif = $dashboardData['semesterAktif'] ?? null;

        $rataRataNilaiSekolah = null;
        if ($semesterAktif) {
            $rataRataNilaiSekolah = Nilai::whereHas(
                'penilaian.mengajar',
                fn ($query) => $query->where('semester_id', $semesterAktif->id)
            )->avg('nilai');
        }

        $dashboardData['rataRataNilaiSekolah'] = $rataRataNilaiSekolah !== null
            ? round((float) $rataRataNilaiSekolah, 2)
            : 0;

        return view(
            'dashboard.kepala-sekolah',
            $dashboardData
        );
    }

    public function waliKelas(): View
    {
        $guru = Auth::user()->guru;

        abort_unless(
            $guru && $guru->isWaliKelasAktif(),
            403,
            'Anda tidak memiliki penugasan sebagai wali kelas aktif.'
        );

        $kelasWali = $guru->kelasWaliAktif();
        $kelasWali->load([
            'kelas.jurusan',
            'tahunAkademik',
            'anggotaKelas.siswa',
        ]);

        $semesterAktif = Semester::aktif()
            ->where('tahun_akademik_id', $kelasWali->tahun_akademik_id)
            ->with('tahunAkademik')
            ->first() ?? Semester::with('tahunAkademik')->latest('id')->first();

        $anggotaKelas = $kelasWali->anggotaKelas->sortBy(fn ($a) => $a->siswa?->nama)->values();
        $totalSiswa = $anggotaKelas->count();
        $siswaIds = $anggotaKelas->pluck('siswa_id');

        $jumlahMapel = Mengajar::where('kelas_akademik_id', $kelasWali->id)
            ->when($semesterAktif, fn ($q) => $q->where('semester_id', $semesterAktif->id))
            ->count();

        // Rata-rata nilai kelas
        $rataRataKelas = Nilai::whereIn('siswa_id', $siswaIds)
            ->whereHas('penilaian.mengajar', fn ($q) => 
                $q->where('kelas_akademik_id', $kelasWali->id)
                  ->when($semesterAktif, fn ($sq) => $sq->where('semester_id', $semesterAktif->id))
            )->avg('nilai');
        $rataRataKelas = $rataRataKelas !== null ? round((float) $rataRataKelas, 1) : 0;

        // Presensi rombel
        $absensis = Absensi::with(['siswa', 'pertemuan.mengajar.mataPelajaran'])
            ->whereIn('siswa_id', $siswaIds)
            ->whereHas('pertemuan.mengajar', fn ($q) => 
                $q->where('kelas_akademik_id', $kelasWali->id)
                  ->when($semesterAktif, fn ($sq) => $sq->where('semester_id', $semesterAktif->id))
            )->get();

        $totalPresensi = $absensis->count();
        $hadirCount = $absensis->where('status', 'hadir')->count();
        $terlambatCount = $absensis->where('status', 'terlambat')->count();
        $sakitCount = $absensis->where('status', 'sakit')->count();
        $izinCount = $absensis->where('status', 'izin')->count();
        $alpaCount = $absensis->where('status', 'alpa')->count();
        $persentaseKehadiran = $totalPresensi > 0
            ? round((($hadirCount + $terlambatCount) / $totalPresensi) * 100, 1)
            : 100;

        // Progress Catatan Wali Kelas
        $totalCatatanDiisi = \App\Models\CatatanWaliKelas::where('kelas_akademik_id', $kelasWali->id)
            ->when($semesterAktif, fn ($q) => $q->where('semester_id', $semesterAktif->id))
            ->count();
        $persenCatatan = $totalSiswa > 0 ? round(($totalCatatanDiisi / $totalSiswa) * 100) : 0;

        // Monitoring Per Siswa
        $siswaMonitoring = $anggotaKelas->map(function ($anggota) use ($absensis, $semesterAktif, $kelasWali) {
            $siswa = $anggota->siswa;
            $siswaAbsensi = $absensis->where('siswa_id', $siswa->id);
            $totalAbsensiSiswa = $siswaAbsensi->count();
            $hadirSiswa = $siswaAbsensi->whereIn('status', ['hadir', 'terlambat'])->count();
            $alpaSiswa = $siswaAbsensi->where('status', 'alpa')->count();
            $persenHadirSiswa = $totalAbsensiSiswa > 0 ? round(($hadirSiswa / $totalAbsensiSiswa) * 100, 1) : 100;

            $hasCatatan = \App\Models\CatatanWaliKelas::where('siswa_id', $siswa->id)
                ->where('kelas_akademik_id', $kelasWali->id)
                ->when($semesterAktif, fn ($q) => $q->where('semester_id', $semesterAktif->id))
                ->exists();

            return [
                'siswa' => $siswa,
                'persen_hadir' => $persenHadirSiswa,
                'alpa' => $alpaSiswa,
                'has_catatan' => $hasCatatan,
            ];
        });

        $recentAbsensi = $absensis->sortByDesc(fn ($a) => $a->pertemuan?->tanggal?->timestamp ?? 0)->take(6)->values();

        return view('dashboard.wali-kelas', compact(
            'guru',
            'kelasWali',
            'semesterAktif',
            'totalSiswa',
            'jumlahMapel',
            'rataRataKelas',
            'persentaseKehadiran',
            'totalPresensi',
            'hadirCount',
            'terlambatCount',
            'sakitCount',
            'izinCount',
            'alpaCount',
            'totalCatatanDiisi',
            'persenCatatan',
            'siswaMonitoring',
            'recentAbsensi'
        ));
    }

    private function hariIndonesia(): string
    {
        return match (now()->dayOfWeekIso) {
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            default => 'minggu',
        };
    }
}
