<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $semesterId = $request->integer('semester_id');

        if (! $semesterId) {
            $semesterId = Semester::aktif()->value('id');
        }

        $jadwals = Jadwal::with([
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereHas(
                'mengajar',
                function ($query) use (
                    $guru,
                    $semesterId
                ) {
                    $query->where(
                        'guru_id',
                        $guru->id
                    );

                    if ($semesterId) {
                        $query->where(
                            'semester_id',
                            $semesterId
                        );
                    }
                }
            )
            ->when(
                $request->filled('hari'),
                fn ($query) => $query->where(
                    'hari',
                    $request->string('hari')->toString()
                )
            )
            ->orderByRaw("
                CASE hari
                    WHEN 'senin' THEN 1
                    WHEN 'selasa' THEN 2
                    WHEN 'rabu' THEN 3
                    WHEN 'kamis' THEN 4
                    WHEN 'jumat' THEN 5
                    WHEN 'sabtu' THEN 6
                    ELSE 7
                END
            ")
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        $semesters = Semester::with('tahunAkademik')
            ->whereHas(
                'mengajars',
                fn ($query) => $query->where(
                    'guru_id',
                    $guru->id
                )
            )
            ->orderByDesc('tanggal_mulai')
            ->get();

        return view(
            'guru.jadwal.index',
            compact(
                'jadwals',
                'semesters',
                'semesterId'
            )
        );
    }
}