<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Services\RaporService;
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
        $siswa = Auth::user()->siswa;

        abort_if(! $siswa, 403, 'Data siswa tidak ditemukan.');

        $semesterId = $request->integer('semester_id');
        $semesterAktif = $semesterId
            ? Semester::with('tahunAkademik')->find($semesterId)
            : Semester::aktif()->with('tahunAkademik')->first();

        $semesters = Semester::with('tahunAkademik')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $raporData = [];
        $isRaporOpen = $semesterAktif?->is_rapor_open ?? false;

        if ($semesterAktif) {
            $raporData = $this->raporService->getRaporSiswa($siswa, $semesterAktif->id);
        }

        return view('siswa.rapor.index', [
            'siswa' => $siswa,
            'semesterAktif' => $semesterAktif,
            'semesters' => $semesters,
            'isRaporOpen' => $isRaporOpen,
            'raporData' => $raporData,
        ]);
    }

    public function cetak(Request $request): View
    {
        $siswa = Auth::user()->siswa;

        abort_if(! $siswa, 403, 'Data siswa tidak ditemukan.');

        $semesterId = $request->integer('semester_id');
        $semester = $semesterId
            ? Semester::with('tahunAkademik')->find($semesterId)
            : Semester::aktif()->with('tahunAkademik')->first();

        abort_if(
            ! $semester || ! $semester->is_rapor_open,
            403,
            'Pencetakan rapor belum dibuka oleh Bagian Tata Usaha.'
        );

        $raporData = $this->raporService->getRaporSiswa($siswa, $semester->id);

        return view('wali-kelas.rapor.cetak', $raporData);
    }
}
