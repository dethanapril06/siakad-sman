<?php

namespace App\Http\Controllers\Siswa;

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
        $siswa = Auth::user()->siswa;

        abort_if(
            ! $siswa,
            403,
            'Data siswa tidak ditemukan.'
        );

        $anggotaKelasAktif = $siswa->kelasAktif();

        $kelasAkademik = $anggotaKelasAktif
            ?->kelasAkademik;

        $semesterId = $request->integer(
            'semester_id'
        );

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->when(
                    $kelasAkademik,
                    fn ($query) => $query->where(
                        'tahun_akademik_id',
                        $kelasAkademik
                            ->tahun_akademik_id
                    )
                )
                ->value('id');
        }

        $jadwals = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        if ($kelasAkademik) {
            $jadwals = Jadwal::with([
                'mengajar.semester.tahunAkademik',
                'mengajar.guru',
                'mengajar.mataPelajaran',
                'ruangan',
            ])
                ->whereHas(
                    'mengajar',
                    function ($query) use (
                        $kelasAkademik,
                        $semesterId
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
                    }
                )
                ->when(
                    $request->filled('hari'),
                    fn ($query) => $query->where(
                        'hari',
                        $request
                            ->string('hari')
                            ->toString()
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
        }

        $semesters = collect();

        if ($kelasAkademik) {
            $semesters = Semester::with(
                'tahunAkademik'
            )
                ->where(
                    'tahun_akademik_id',
                    $kelasAkademik
                        ->tahun_akademik_id
                )
                ->orderBy('tanggal_mulai')
                ->get();
        }

        return view(
            'siswa.jadwal.index',
            compact(
                'siswa',
                'kelasAkademik',
                'jadwals',
                'semesters',
                'semesterId'
            )
        );
    }
}