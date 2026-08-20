<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasAkademik;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KelasAkademikController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id');

        if (! $tahunAkademikId) {
            $tahunAkademikId = TahunAkademik::aktif()->value('id');
        }

        $kelasAkademiks = KelasAkademik::query()
            ->select('kelas_akademiks.*')
            ->with([
                'kelas.jurusan',
                'tahunAkademik',
                'waliKelas',
            ])
            ->withCount('anggotaKelas')
            ->when($tahunAkademikId, function ($query) use ($tahunAkademikId) {
                $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->whereHas('kelas', function ($query) use ($search) {
                        $query->where('nama', 'like', "%{$search}%")
                            ->orWhere('tingkat', 'like', "%{$search}%");
                    })
                        ->orWhereHas('kelas.jurusan', function ($query) use ($search) {
                            $query->where('kode', 'like', "%{$search}%")
                                ->orWhere('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('waliKelas', function ($query) use ($search) {
                            $query->where('nama', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%");
                        });
                });
            })
            ->leftJoin('kelas', 'kelas_akademiks.kelas_id', '=', 'kelas.id')
            ->leftJoin('jurusans', 'kelas.jurusan_id', '=', 'jurusans.id')
            ->orderByRaw(
                "CASE kelas.tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 4 END"
            )
            ->orderBy('jurusans.kode')
            ->orderBy('kelas.nama')
            ->paginate(10)
            ->withQueryString();

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        return view(
            'akademik.kelas-akademik.index',
            compact(
                'kelasAkademiks',
                'tahunAkademiks',
                'tahunAkademikId'
            )
        );
    }

    public function create(): View
    {
        $kelas = Kelas::aktif()
            ->with('jurusan')
            ->get();

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        $gurus = Guru::where('status', 'aktif')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.kelas-akademik.create',
            compact(
                'kelas',
                'tahunAkademiks',
                'gurus'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],
            'tahun_akademik_id' => [
                'required',
                'exists:tahun_akademiks,id',
            ],
            'wali_kelas_id' => [
                'nullable',
                'exists:gurus,id',
            ],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'tahun_akademik_id.required' => 'Tahun akademik wajib dipilih.',
            'tahun_akademik_id.exists' => 'Tahun akademik tidak ditemukan.',
            'wali_kelas_id.exists' => 'Guru wali kelas tidak ditemukan.',
        ]);

        $exists = KelasAkademik::where(
            'kelas_id',
            $validated['kelas_id']
        )
            ->where(
                'tahun_akademik_id',
                $validated['tahun_akademik_id']
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas tersebut sudah terdaftar pada tahun akademik yang dipilih.',
            ]);
        }

        $this->validateWaliKelas(
            $validated['wali_kelas_id'] ?? null,
            $validated['tahun_akademik_id']
        );

        KelasAkademik::create([
            'kelas_id' => $validated['kelas_id'],
            'tahun_akademik_id' => $validated['tahun_akademik_id'],
            'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
        ]);

        return redirect()
            ->route('pegawai-tu.akademik.kelas-akademik.index')
            ->with(
                'success',
                'Kelas akademik berhasil ditambahkan.'
            );
    }

    public function show(
        KelasAkademik $kelasAkademik
    ): View {
        $kelasAkademik->load([
            'kelas.jurusan',
            'tahunAkademik',
            'waliKelas',
            'anggotaKelas.siswa',
        ]);

        return view(
            'akademik.kelas-akademik.show',
            compact('kelasAkademik')
        );
    }

    public function edit(
        KelasAkademik $kelasAkademik
    ): View {
        $kelas = Kelas::aktif()
            ->with('jurusan')
            ->get();

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        $gurus = Guru::where('status', 'aktif')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.kelas-akademik.edit',
            compact(
                'kelasAkademik',
                'kelas',
                'tahunAkademiks',
                'gurus'
            )
        );
    }

    public function update(
        Request $request,
        KelasAkademik $kelasAkademik
    ): RedirectResponse {
        $validated = $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],
            'tahun_akademik_id' => [
                'required',
                'exists:tahun_akademiks,id',
            ],
            'wali_kelas_id' => [
                'nullable',
                'exists:gurus,id',
            ],
        ]);

        $exists = KelasAkademik::where(
            'kelas_id',
            $validated['kelas_id']
        )
            ->where(
                'tahun_akademik_id',
                $validated['tahun_akademik_id']
            )
            ->whereKeyNot($kelasAkademik->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas tersebut sudah terdaftar pada tahun akademik yang dipilih.',
            ]);
        }

        $this->validateWaliKelas(
            $validated['wali_kelas_id'] ?? null,
            $validated['tahun_akademik_id'],
            $kelasAkademik->id
        );

        if (
            $kelasAkademik->tahun_akademik_id
            !== (int) $validated['tahun_akademik_id']
            && (
                $kelasAkademik->anggotaKelas()->exists()
                || $kelasAkademik->mengajars()->exists()
            )
        ) {
            throw ValidationException::withMessages([
                'tahun_akademik_id' => 'Tahun akademik tidak dapat diubah karena kelas sudah memiliki anggota atau data mengajar.',
            ]);
        }

        $kelasAkademik->update([
            'kelas_id' => $validated['kelas_id'],
            'tahun_akademik_id' => $validated['tahun_akademik_id'],
            'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
        ]);

        return redirect()
            ->route('pegawai-tu.akademik.kelas-akademik.index')
            ->with(
                'success',
                'Kelas akademik berhasil diperbarui.'
            );
    }

    public function destroy(
        KelasAkademik $kelasAkademik
    ): RedirectResponse {
        if (
            $kelasAkademik->anggotaKelas()->exists()
            || $kelasAkademik->mengajars()->exists()
        ) {
            return back()->with(
                'error',
                'Kelas akademik tidak dapat dihapus karena sudah memiliki data akademik terkait.'
            );
        }

        $kelasAkademik->delete();

        return redirect()
            ->route('pegawai-tu.akademik.kelas-akademik.index')
            ->with(
                'success',
                'Kelas akademik berhasil dihapus.'
            );
    }

    private function validateWaliKelas(
        ?int $waliKelasId,
        int $tahunAkademikId,
        ?int $exceptKelasAkademikId = null
    ): void {
        if (! $waliKelasId) {
            return;
        }

        $guru = Guru::findOrFail($waliKelasId);

        if (
            $guru->status !== 'aktif'
            || ! $guru->user?->is_active
        ) {
            throw ValidationException::withMessages([
                'wali_kelas_id' => 'Guru yang dipilih tidak aktif.',
            ]);
        }

        $query = KelasAkademik::where(
            'tahun_akademik_id',
            $tahunAkademikId
        )
            ->where(
                'wali_kelas_id',
                $waliKelasId
            );

        if ($exceptKelasAkademikId) {
            $query->whereKeyNot($exceptKelasAkademikId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'wali_kelas_id' => 'Guru tersebut sudah menjadi wali kelas pada kelas lain di tahun akademik yang sama.',
            ]);
        }
    }
}