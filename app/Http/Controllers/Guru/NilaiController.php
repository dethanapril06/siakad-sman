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
            'mengajar.kelasAkademik.anggotaKelas' => function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('status', 'aktif');
                });
            },
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
            ->whereHas('siswa', function ($q) {
                $q->where('status', 'aktif');
            })
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
                'nilai' => 'Terdapat siswa yang bukan anggota kelas aktif pada penilaian ini.',
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
                'nilai' => "Nilai belum diisi untuk siswa aktif: {$namaSiswas}.",
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

    public function editSiswa(\App\Models\Mengajar $mengajar, Siswa $siswa): View
    {
        $guru = Auth::user()->guru;
        abort_unless($mengajar->guru_id === $guru?->id, 403, 'Anda tidak memiliki akses ke penugasan ini.');

        $mengajar->load([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'penilaians.jenisNilai',
        ]);

        $isMember = $mengajar->kelasAkademik->anggotaKelas()
            ->where('siswa_id', $siswa->id)
            ->exists();

        abort_unless($isMember, 404, 'Siswa tidak terdaftar pada kelas akademik ini.');

        $penilaians = $mengajar->penilaians()->with('jenisNilai')->orderBy('jenis_nilai_id')->get();

        $nilais = Nilai::where('siswa_id', $siswa->id)
            ->whereIn('penilaian_id', $penilaians->pluck('id'))
            ->get()
            ->keyBy('penilaian_id');

        $jenisNilais = \App\Models\JenisNilai::aktif()->get();
        $bobotMap = \App\Models\JenisNilai::getBobotMap();

        return view('guru.nilai.siswa', compact(
            'mengajar',
            'siswa',
            'penilaians',
            'nilais',
            'jenisNilais',
            'bobotMap'
        ));
    }

    public function updateSiswa(Request $request, \App\Models\Mengajar $mengajar, Siswa $siswa): RedirectResponse
    {
        $guru = Auth::user()->guru;
        abort_unless($mengajar->guru_id === $guru?->id, 403, 'Anda tidak memiliki akses ke penugasan ini.');

        $validated = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $penilaians = $mengajar->penilaians()->get();
        $penilaianIds = $penilaians->pluck('id')->all();

        DB::transaction(function () use ($validated, $penilaianIds, $siswa) {
            foreach ($penilaianIds as $penilaianId) {
                $nilaiVal = $validated['nilai'][$penilaianId] ?? null;
                $catatanVal = $validated['catatan'][$penilaianId] ?? null;

                if ($nilaiVal !== null && $nilaiVal !== '') {
                    Nilai::updateOrCreate(
                        [
                            'penilaian_id' => $penilaianId,
                            'siswa_id' => $siswa->id,
                        ],
                        [
                            'nilai' => $nilaiVal,
                            'catatan' => $catatanVal,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('guru.nilai.siswa', ['mengajar' => $mengajar->id, 'siswa' => $siswa->id])
            ->with('success', "Nilai untuk {$siswa->nama} berhasil disimpan.");
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
