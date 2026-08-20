<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Penilaian;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function edit(
        Penilaian $penilaian
    ): View {
        $this->authorizePenilaian($penilaian);

        $penilaian->load([
            'jenisNilai',
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.kelasAkademik.anggotaKelas.siswa',
            'mengajar.mataPelajaran',
            'nilais',
        ]);

        $nilaiExisting = $penilaian
            ->nilais
            ->keyBy('siswa_id');

        return view(
            'guru.nilai.edit',
            compact(
                'penilaian',
                'nilaiExisting'
            )
        );
    }

    public function update(
        Request $request,
        Penilaian $penilaian
    ): RedirectResponse {
        $this->authorizePenilaian($penilaian);

        $validated = $request->validate([
            'nilai' => [
                'required',
                'array',
                'min:1',
            ],
            'nilai.*.siswa_id' => [
                'required',
                'distinct',
                'exists:siswas,id',
            ],
            'nilai.*.nilai' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'nilai.*.catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'nilai.required' => 'Data nilai wajib diisi.',
            'nilai.*.nilai.required' => 'Nilai siswa wajib diisi.',
            'nilai.*.nilai.min' => 'Nilai minimal adalah 0.',
            'nilai.*.nilai.max' => 'Nilai maksimal adalah 100.',
        ]);

        $kelasAkademik = $penilaian
            ->mengajar
            ->kelasAkademik;

        $siswaIdsKelas = $kelasAkademik
            ->anggotaKelas()
            ->pluck('siswa_id');

        $siswaIdsInput = collect(
            $validated['nilai']
        )
            ->pluck('siswa_id')
            ->map(fn ($id) => (int) $id);

        $siswaTidakValid = $siswaIdsInput->diff(
            $siswaIdsKelas
        );

        if ($siswaTidakValid->isNotEmpty()) {
            throw ValidationException::withMessages([
                'nilai' => 'Terdapat siswa yang bukan anggota kelas pada penilaian ini.',
            ]);
        }

        $siswaBelumDinilai = $siswaIdsKelas->diff(
            $siswaIdsInput
        );

        if ($siswaBelumDinilai->isNotEmpty()) {
            $namaSiswas = Siswa::whereIn(
                'id',
                $siswaBelumDinilai
            )
                ->pluck('nama')
                ->implode(', ');

            throw ValidationException::withMessages([
                'nilai' => "Nilai belum diisi untuk siswa: {$namaSiswas}.",
            ]);
        }

        DB::transaction(function () use (
            $validated,
            $penilaian
        ): void {
            foreach ($validated['nilai'] as $item) {
                Nilai::updateOrCreate(
                    [
                        'penilaian_id' => $penilaian->id,
                        'siswa_id' => $item['siswa_id'],
                    ],
                    [
                        'nilai' => $item['nilai'],
                        'catatan' => $item['catatan'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('guru.penilaian.show', $penilaian)
            ->with(
                'success',
                'Nilai siswa berhasil disimpan.'
            );
    }

    private function authorizePenilaian(
        Penilaian $penilaian
    ): void {
        $penilaian->loadMissing(
            'mengajar.kelasAkademik'
        );

        abort_unless(
            $penilaian->mengajar->guru_id
                === Auth::user()->guru?->id,
            403,
            'Anda tidak memiliki akses untuk mengelola nilai pada penilaian ini.'
        );
    }
}
