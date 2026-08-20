<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\KelasAkademik;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnggotaKelasController extends Controller
{
    public function index(
        Request $request,
        KelasAkademik $kelasAkademik
    ): View {
        $kelasAkademik->load([
            'kelas.jurusan',
            'tahunAkademik',
            'waliKelas',
        ]);

        $anggotaKelas = AnggotaKelas::with('siswa.user')
            ->where(
                'kelas_akademik_id',
                $kelasAkademik->id
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')
                    ->trim()
                    ->toString();

                $query->whereHas('siswa', function ($query) use ($search) {
                    $query->where('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderBy(
                Siswa::select('nama')
                    ->whereColumn(
                        'siswas.id',
                        'anggota_kelas.siswa_id'
                    )
            )
            ->paginate(10)
            ->withQueryString();

        return view(
            'akademik.anggota-kelas.index',
            compact(
                'kelasAkademik',
                'anggotaKelas'
            )
        );
    }

    public function create(
        KelasAkademik $kelasAkademik
    ): View {
        $kelasAkademik->load([
            'kelas.jurusan',
            'tahunAkademik',
        ]);

        $tahunAkademikId = $kelasAkademik
            ->tahun_akademik_id;

        $siswaIdsSudahMemilikiKelas = AnggotaKelas::whereHas(
            'kelasAkademik',
            function ($query) use ($tahunAkademikId) {
                $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                );
            }
        )
            ->pluck('siswa_id');

        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->whereNotIn(
                'id',
                $siswaIdsSudahMemilikiKelas
            )
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.anggota-kelas.create',
            compact(
                'kelasAkademik',
                'siswas'
            )
        );
    }

    public function store(
        Request $request,
        KelasAkademik $kelasAkademik
    ): RedirectResponse {
        $validated = $request->validate([
            'siswa_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'siswa_ids.*' => [
                'required',
                'distinct',
                'exists:siswas,id',
            ],
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa.',
            'siswa_ids.array' => 'Data siswa tidak valid.',
            'siswa_ids.min' => 'Pilih minimal satu siswa.',
            'siswa_ids.*.exists' => 'Terdapat siswa yang tidak ditemukan.',
            'siswa_ids.*.distinct' => 'Data siswa tidak boleh duplikat.',
        ]);

        $tahunAkademikId = $kelasAkademik
            ->tahun_akademik_id;

        $siswas = Siswa::whereIn(
            'id',
            $validated['siswa_ids']
        )->get();

        foreach ($siswas as $siswa) {
            if ($siswa->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'siswa_ids' => "Siswa {$siswa->nama} berstatus tidak aktif.",
                ]);
            }

            $anggotaExisting = AnggotaKelas::with([
                'kelasAkademik.kelas.jurusan',
            ])
                ->where('siswa_id', $siswa->id)
                ->whereHas(
                    'kelasAkademik',
                    function ($query) use ($tahunAkademikId) {
                        $query->where(
                            'tahun_akademik_id',
                            $tahunAkademikId
                        );
                    }
                )
                ->first();

            if ($anggotaExisting) {
                $namaKelas = $anggotaExisting
                    ->kelasAkademik
                    ->nama_lengkap;

                throw ValidationException::withMessages([
                    'siswa_ids' => "Siswa {$siswa->nama} sudah terdaftar pada kelas {$namaKelas} di tahun akademik yang sama.",
                ]);
            }
        }

        DB::transaction(function () use (
            $validated,
            $kelasAkademik
        ): void {
            foreach ($validated['siswa_ids'] as $siswaId) {
                AnggotaKelas::create([
                    'kelas_akademik_id' => $kelasAkademik->id,
                    'siswa_id' => $siswaId,
                ]);
            }
        });

        return redirect()
            ->route(
                'pegawai-tu.akademik.anggota-kelas.index',
                $kelasAkademik
            )
            ->with(
                'success',
                'Siswa berhasil ditambahkan ke kelas.'
            );
    }

    public function pindahForm(
        AnggotaKelas $anggotaKelas
    ): View {
        $anggotaKelas->load([
            'siswa',
            'kelasAkademik.kelas.jurusan',
            'kelasAkademik.tahunAkademik',
        ]);

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
        ])
            ->where(
                'tahun_akademik_id',
                $anggotaKelas
                    ->kelasAkademik
                    ->tahun_akademik_id
            )
            ->whereKeyNot(
                $anggotaKelas->kelas_akademik_id
            )
            ->get();

        return view(
            'akademik.anggota-kelas.pindah',
            compact(
                'anggotaKelas',
                'kelasAkademiks'
            )
        );
    }

    public function pindah(
        Request $request,
        AnggotaKelas $anggotaKelas
    ): RedirectResponse {
        $validated = $request->validate([
            'kelas_akademik_id' => [
                'required',
                'exists:kelas_akademiks,id',
            ],
        ]);

        $kelasTujuan = KelasAkademik::findOrFail(
            $validated['kelas_akademik_id']
        );

        if (
            $kelasTujuan->tahun_akademik_id
            !== $anggotaKelas
                ->kelasAkademik
                ->tahun_akademik_id
        ) {
            throw ValidationException::withMessages([
                'kelas_akademik_id' => 'Pemindahan kelas hanya dapat dilakukan dalam tahun akademik yang sama.',
            ]);
        }

        if (
            $kelasTujuan->id
            === $anggotaKelas->kelas_akademik_id
        ) {
            throw ValidationException::withMessages([
                'kelas_akademik_id' => 'Kelas tujuan harus berbeda dari kelas saat ini.',
            ]);
        }

        $anggotaKelas->update([
            'kelas_akademik_id' => $kelasTujuan->id,
        ]);

        return redirect()
            ->route(
                'pegawai-tu.akademik.anggota-kelas.index',
                $kelasTujuan
            )
            ->with(
                'success',
                'Siswa berhasil dipindahkan ke kelas tujuan.'
            );
    }

    public function destroy(
        AnggotaKelas $anggotaKelas
    ): RedirectResponse {
        $kelasAkademik = $anggotaKelas
            ->kelasAkademik;

        $siswa = $anggotaKelas->siswa;

        $memilikiAbsensi = $siswa->absensis()
            ->whereHas(
                'pertemuan.mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasAkademik->id
                )
            )
            ->exists();

        $memilikiNilai = $siswa->nilais()
            ->whereHas(
                'penilaian.mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $kelasAkademik->id
                )
            )
            ->exists();

        if ($memilikiAbsensi || $memilikiNilai) {
            return back()->with(
                'error',
                'Siswa tidak dapat dikeluarkan dari kelas karena sudah memiliki data absensi atau nilai pada kelas tersebut.'
            );
        }

        $anggotaKelas->delete();

        return redirect()
            ->route(
                'pegawai-tu.akademik.anggota-kelas.index',
                $kelasAkademik
            )
            ->with(
                'success',
                'Siswa berhasil dikeluarkan dari kelas.'
            );
    }
}