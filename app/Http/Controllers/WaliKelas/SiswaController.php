<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SiswaController extends Controller
{
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

        $siswas = Siswa::query()
            ->whereHas(
                'anggotaKelas',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasWali->id
                )
            )
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request
                        ->string('search')
                        ->trim()
                        ->toString();

                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'nis',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'nisn',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'nama',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('jenis_kelamin'),
                fn ($query) => $query->where(
                    'jenis_kelamin',
                    $request
                        ->string('jenis_kelamin')
                        ->toString()
                )
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request
                        ->string('status')
                        ->toString()
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $kelasWali->load([
            'kelas.jurusan',
            'tahunAkademik',
        ]);

        return view(
            'wali-kelas.siswa.index',
            compact(
                'kelasWali',
                'siswas'
            )
        );
    }

    public function show(Siswa $siswa): View
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

        $this->authorizeSiswaKelas(
            $siswa,
            $kelasWali->id
        );

        $siswa->load([
            'user',
            'anggotaKelas' => function ($query) use (
                $kelasWali
            ) {
                $query->where(
                    'kelas_akademik_id',
                    $kelasWali->id
                );
            },
            'anggotaKelas.kelasAkademik.kelas.jurusan',
            'anggotaKelas.kelasAkademik.tahunAkademik',
        ]);

        return view(
            'wali-kelas.siswa.show',
            compact(
                'kelasWali',
                'siswa'
            )
        );
    }

    private function authorizeSiswaKelas(
        Siswa $siswa,
        int $kelasAkademikId
    ): void {
        $isAnggota = AnggotaKelas::where(
            'kelas_akademik_id',
            $kelasAkademikId
        )
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->exists();

        abort_unless(
            $isAnggota,
            403,
            'Siswa tersebut bukan anggota kelas yang menjadi tanggung jawab Anda.'
        );
    }
}