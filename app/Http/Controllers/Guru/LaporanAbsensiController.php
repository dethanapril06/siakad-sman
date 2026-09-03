<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\Mengajar;
use App\Models\Pertemuan;
use App\Models\Sekolah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;
        abort_if(! $guru, 403, 'Data guru tidak ditemukan.');

        $semesterAktif = Semester::aktif()->first();

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->where('guru_id', $guru->id)
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->when(! $request->filled('semester_id') && $semesterAktif, fn ($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderByDesc('semester_id')
            ->get();

        $selectedMengajarId = $request->integer('mengajar_id') ?: $mengajars->first()?->id;
        $selectedMengajar = $selectedMengajarId ? $mengajars->firstWhere('id', $selectedMengajarId) : null;

        if ($selectedMengajarId && ! $selectedMengajar) {
            $selectedMengajar = Mengajar::with([
                'semester.tahunAkademik',
                'kelasAkademik.kelas.jurusan',
                'mataPelajaran',
            ])->where('guru_id', $guru->id)->findOrFail($selectedMengajarId);
        }

        $reportData = $this->buildReportData($selectedMengajar);

        return view('guru.laporan-absensi.index', [
            'mengajars' => $mengajars,
            'selectedMengajar' => $selectedMengajar,
            'selectedMengajarId' => $selectedMengajarId,
            'rekapSiswa' => $reportData['rekapSiswa'],
            'totalPertemuan' => $reportData['totalPertemuan'],
        ]);
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
        ])->where('guru_id', $guru->id)->findOrFail($mengajarId);

        $reportData = $this->buildReportData($selectedMengajar);
        $kepalaSekolah = Sekolah::first()?->kepalaSekolah ?? Guru::where('status', 'aktif')->first();

        return view('guru.laporan-absensi.cetak', [
            'selectedMengajar' => $selectedMengajar,
            'rekapSiswa' => $reportData['rekapSiswa'],
            'totalPertemuan' => $reportData['totalPertemuan'],
            'guru' => $guru,
            'kepalaSekolah' => $kepalaSekolah,
        ]);
    }

    private function buildReportData(?Mengajar $selectedMengajar): array
    {
        if (! $selectedMengajar) {
            return [
                'rekapSiswa' => collect(),
                'totalPertemuan' => 0,
            ];
        }

        $anggotaKelas = AnggotaKelas::with('siswa')
            ->where('kelas_akademik_id', $selectedMengajar->kelas_akademik_id)
            ->get()
            ->sortBy(fn ($a) => $a->siswa?->nama)
            ->values();

        $pertemuans = Pertemuan::where('mengajar_id', $selectedMengajar->id)->get();
        $totalPertemuan = $pertemuans->count();

        $absensis = Absensi::whereIn('pertemuan_id', $pertemuans->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        $rekapSiswa = $anggotaKelas->map(function (AnggotaKelas $anggota, int $index) use ($absensis, $totalPertemuan) {
            $siswaAbsen = $absensis->get($anggota->siswa_id, collect());

            $hadir = $siswaAbsen->where('status', 'hadir')->count();
            $terlambat = $siswaAbsen->where('status', 'terlambat')->count();
            $sakit = $siswaAbsen->where('status', 'sakit')->count();
            $izin = $siswaAbsen->where('status', 'izin')->count();
            $alpa = $siswaAbsen->where('status', 'alpa')->count();

            $totalHadir = $hadir + $terlambat;
            $persentase = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 0;

            return [
                'no' => $index + 1,
                'siswa' => $anggota->siswa,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'total_hadir' => $totalHadir,
                'persentase' => $persentase,
            ];
        });

        return [
            'rekapSiswa' => $rekapSiswa,
            'totalPertemuan' => $totalPertemuan,
        ];
    }
}
