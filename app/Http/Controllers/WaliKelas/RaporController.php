<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\CatatanWaliKelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Services\RaporService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RaporController extends Controller
{
    public function __construct(
        protected RaporService $raporService
    ) {}

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

        $semesterId = $request->integer('semester_id');

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->where('tahun_akademik_id', $kelasWali->tahun_akademik_id)
                ->value('id');
        }

        $semesterAktif = $semesterId
            ? Semester::with('tahunAkademik')->find($semesterId)
            : Semester::aktif()->first();

        $semesters = Semester::with('tahunAkademik')
            ->where('tahun_akademik_id', $kelasWali->tahun_akademik_id)
            ->orderBy('tanggal_mulai')
            ->get();

        $anggotaKelas = AnggotaKelas::with(['siswa.catatanWaliKelas' => function ($q) use ($semesterId, $kelasWali) {
            $q->where('semester_id', $semesterId)->where('kelas_akademik_id', $kelasWali->id);
        }])
            ->where('kelas_akademik_id', $kelasWali->id)
            ->get()
            ->sortBy(fn (AnggotaKelas $a) => $a->siswa?->nama)
            ->values();

        // Data rekap ringkas tiap siswa untuk tabel wali kelas
        $ledger = $semesterId ? $this->raporService->getLedgerKelas($kelasWali->id, $semesterId) : [];

        // Buat map ranking & rata-rata
        $siswaStats = [];
        foreach ($ledger as $rank => $item) {
            $siswaStats[$item['siswa']->id] = [
                'rank' => $rank + 1,
                'rata_rata' => $item['rata_rata'],
                'total_nilai' => $item['total_nilai'],
                'presensi' => $item['presensi'],
            ];
        }

        return view(
            'wali-kelas.rapor.index',
            compact(
                'kelasWali',
                'semesterAktif',
                'semesters',
                'anggotaKelas',
                'siswaStats'
            )
        );
    }

    public function updateCatatan(Request $request, Siswa $siswa): RedirectResponse
    {
        $guru = Auth::user()->guru;
        $kelasWali = $guru?->kelasWaliAktif();

        abort_unless($kelasWali, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'catatan' => 'nullable|string|max:1000',
            'status_kenaikan' => 'nullable|string|max:100',
        ]);

        CatatanWaliKelas::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'semester_id' => $validated['semester_id'],
                'kelas_akademik_id' => $kelasWali->id,
            ],
            [
                'catatan' => $validated['catatan'],
                'status_kenaikan' => $validated['status_kenaikan'],
            ]
        );

        return back()->with('success', "Catatan untuk siswa {$siswa->nama} berhasil disimpan.");
    }

    public function cetakSiswa(Request $request, Siswa $siswa): View
    {
        $guru = Auth::user()->guru;
        $kelasWali = $guru?->kelasWaliAktif();

        abort_unless($kelasWali, 403, 'Akses ditolak.');

        $semesterId = $request->integer('semester_id');
        $raporData = $this->raporService->getRaporSiswa($siswa, $semesterId, $kelasWali->id);

        return view('wali-kelas.rapor.cetak', $raporData);
    }
}
