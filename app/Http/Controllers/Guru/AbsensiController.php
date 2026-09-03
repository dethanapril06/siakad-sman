<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Mengajar;
use App\Models\Pertemuan;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(
        Request $request
    ): View {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $pertemuans = Pertemuan::with([
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
        ])
            ->withCount('absensis')
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'guru_id',
                    $guru->id
                )
            )
            ->when(
                $request->filled('mengajar_id'),
                fn ($query) => $query->where(
                    'mengajar_id',
                    $request->integer('mengajar_id')
                )
            )
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        $mengajars = Mengajar::with([
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'semester.tahunAkademik',
        ])
            ->where('guru_id', $guru->id)
            ->orderByDesc('semester_id')
            ->get();

        return view(
            'guru.absensi.index',
            compact(
                'pertemuans',
                'mengajars'
            )
        );
    }

    public function edit(
        Pertemuan $pertemuan
    ): View {
        $this->authorizePertemuan($pertemuan);

        $pertemuan->load([
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'mengajar.kelasAkademik.anggotaKelas' => function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('status', 'aktif');
                });
            },
            'mengajar.kelasAkademik.anggotaKelas.siswa',
            'absensis',
        ]);

        $absensiExisting = $pertemuan
            ->absensis
            ->keyBy('siswa_id');

        return view(
            'guru.absensi.edit',
            compact(
                'pertemuan',
                'absensiExisting'
            )
        );
    }

    public function update(
        Request $request,
        Pertemuan $pertemuan
    ): RedirectResponse {
        $this->authorizePertemuan($pertemuan);

        $validated = $request->validate([
            'absensi' => [
                'required',
                'array',
                'min:1',
            ],
            'absensi.*.siswa_id' => [
                'required',
                'distinct',
                'exists:siswas,id',
            ],
            'absensi.*.status' => [
                'required',
                Rule::in([
                    'hadir',
                    'sakit',
                    'izin',
                    'alpa',
                    'terlambat',
                ]),
            ],
            'absensi.*.is_terlambat' => [
                'nullable',
            ],
            'absensi.*.keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'absensi.required' => 'Data absensi wajib diisi.',
            'absensi.*.status.required' => 'Status kehadiran siswa wajib dipilih.',
        ]);

        $kelasAkademik = $pertemuan
            ->mengajar
            ->kelasAkademik;

        $siswaIdsKelas = $kelasAkademik
            ->anggotaKelas()
            ->whereHas('siswa', function ($q) {
                $q->where('status', 'aktif');
            })
            ->pluck('siswa_id');

        $siswaIdsInput = collect(
            $validated['absensi']
        )
            ->pluck('siswa_id')
            ->map(fn ($id) => (int) $id);

        $siswaTidakValid = $siswaIdsInput->diff(
            $siswaIdsKelas
        );

        if ($siswaTidakValid->isNotEmpty()) {
            throw ValidationException::withMessages([
                'absensi' => 'Terdapat siswa yang bukan anggota kelas aktif pada penugasan mengajar ini.',
            ]);
        }

        $siswaBelumDiinput = $siswaIdsKelas->diff(
            $siswaIdsInput
        );

        if ($siswaBelumDiinput->isNotEmpty()) {
            $namaSiswas = Siswa::whereIn(
                'id',
                $siswaBelumDiinput
            )
                ->pluck('nama')
                ->implode(', ');

            throw ValidationException::withMessages([
                'absensi' => "Absensi belum diisi untuk siswa aktif: {$namaSiswas}.",
            ]);
        }

        DB::transaction(function () use (
            $validated,
            $pertemuan
        ): void {
            foreach ($validated['absensi'] as $item) {
                $status = $item['status'];
                $isTerlambat = ! empty($item['is_terlambat']) && in_array($item['is_terlambat'], [1, '1', 'ya', true], true);

                if ($status === 'hadir' && $isTerlambat) {
                    $status = 'terlambat';
                }

                Absensi::updateOrCreate(
                    [
                        'pertemuan_id' => $pertemuan->id,
                        'siswa_id' => $item['siswa_id'],
                    ],
                    [
                        'status' => $status,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('guru.absensi.index')
            ->with(
                'success',
                'Data absensi siswa berhasil disimpan.'
            );
    }

    private function authorizePertemuan(
        Pertemuan $pertemuan
    ): void {
        $pertemuan->loadMissing(
            'mengajar.kelasAkademik'
        );

        abort_unless(
            $pertemuan->mengajar->guru_id
                === Auth::user()->guru?->id,
            403,
            'Anda tidak memiliki akses untuk mengelola absensi pertemuan ini.'
        );
    }
}
