<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Mengajar;
use App\Models\Pertemuan;
use App\Models\Sekolah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanKeterlambatanController extends Controller
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

        $keterlambatans = $this->getKeterlambatanData($selectedMengajar);

        return view('guru.laporan-keterlambatan.index', [
            'mengajars' => $mengajars,
            'selectedMengajar' => $selectedMengajar,
            'selectedMengajarId' => $selectedMengajarId,
            'keterlambatans' => $keterlambatans,
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

        $keterlambatans = $this->getKeterlambatanData($selectedMengajar);
        $kepalaSekolah = Sekolah::first()?->kepalaSekolah ?? Guru::where('status', 'aktif')->first();

        return view('guru.laporan-keterlambatan.cetak', [
            'selectedMengajar' => $selectedMengajar,
            'keterlambatans' => $keterlambatans,
            'guru' => $guru,
            'kepalaSekolah' => $kepalaSekolah,
        ]);
    }

    private function getKeterlambatanData(?Mengajar $selectedMengajar)
    {
        if (! $selectedMengajar) {
            return collect();
        }

        return Absensi::with(['siswa', 'pertemuan'])
            ->where('status', 'terlambat')
            ->whereHas('pertemuan', fn ($q) => $q->where('mengajar_id', $selectedMengajar->id))
            ->join('pertemuans', 'absensis.pertemuan_id', '=', 'pertemuans.id')
            ->orderByDesc('pertemuans.tanggal')
            ->select('absensis.*')
            ->get();
    }
}
