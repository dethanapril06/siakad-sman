<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mengajar;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MengajarController extends Controller
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

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'jadwals.ruangan',
        ])
            ->where('guru_id', $guru->id)
            ->when(
                $semesterId,
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )
            ->orderBy('kelas_akademik_id')
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
            'guru.mengajar.index',
            compact(
                'mengajars',
                'semesters',
                'semesterId'
            )
        );
    }

    public function show(Mengajar $mengajar): View
    {
        $this->authorizeMengajar($mengajar);

        $mengajar->load([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'kelasAkademik.anggotaKelas.siswa',
            'mataPelajaran',
            'jadwals.ruangan',
            'pertemuans',
            'penilaians.jenisNilai',
        ]);

        return view(
            'guru.mengajar.show',
            compact('mengajar')
        );
    }

    private function authorizeMengajar(
        Mengajar $mengajar
    ): void {
        abort_unless(
            $mengajar->guru_id
                === Auth::user()->guru?->id,
            403,
            'Anda tidak memiliki akses ke data mengajar ini.'
        );
    }
}