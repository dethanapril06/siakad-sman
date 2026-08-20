<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Mengajar;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MengajarController extends Controller
{
    public function index(Request $request): View
    {
        $semesterId = $request->integer('semester_id');

        if (! $semesterId) {
            $semesterId = Semester::aktif()->value('id');
        }

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'guru',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->when(
                $semesterId,
                fn ($query) => $query->where(
                    'semester_id',
                    $semesterId
                )
            )
            ->when(
                $request->filled('guru_id'),
                fn ($query) => $query->where(
                    'guru_id',
                    $request->integer('guru_id')
                )
            )
            ->when(
                $request->filled('kelas_akademik_id'),
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $request->integer('kelas_akademik_id')
                )
            )
            ->when(
                $request->filled('mata_pelajaran_id'),
                fn ($query) => $query->where(
                    'mata_pelajaran_id',
                    $request->integer('mata_pelajaran_id')
                )
            )
            ->orderBy('guru_id')
            ->paginate(10)
            ->withQueryString();

        $semesters = Semester::with('tahunAkademik')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])
            ->orderBy('kelas_id')
            ->get();

        $mataPelajarans = MataPelajaran::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.mengajar.index',
            compact(
                'mengajars',
                'semesters',
                'gurus',
                'kelasAkademiks',
                'mataPelajarans',
                'semesterId'
            )
        );
    }

    public function create(): View
    {
        $semesters = Semester::with('tahunAkademik')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $gurus = Guru::where('status', 'aktif')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('nama')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])->get();

        $mataPelajarans = MataPelajaran::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.mengajar.create',
            compact(
                'semesters',
                'gurus',
                'kelasAkademiks',
                'mataPelajarans'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'semester_id' => [
                'required',
                'exists:semesters,id',
            ],
            'guru_id' => [
                'required',
                'exists:gurus,id',
            ],
            'kelas_akademik_id' => [
                'required',
                'exists:kelas_akademiks,id',
            ],
            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id',
            ],
        ]);

        $this->validatePenugasan($validated);

        Mengajar::create($validated);

        return redirect()
            ->route('pegawai-tu.akademik.mengajar.index')
            ->with(
                'success',
                'Penugasan mengajar berhasil ditambahkan.'
            );
    }

    public function show(Mengajar $mengajar): View
    {
        $mengajar->load([
            'semester.tahunAkademik',
            'guru',
            'kelasAkademik.kelas.jurusan',
            'kelasAkademik.tahunAkademik',
            'mataPelajaran',
            'jadwals.ruangan',
        ]);

        return view(
            'akademik.mengajar.show',
            compact('mengajar')
        );
    }

    public function edit(Mengajar $mengajar): View
    {
        $semesters = Semester::with('tahunAkademik')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])->get();

        $mataPelajarans = MataPelajaran::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.mengajar.edit',
            compact(
                'mengajar',
                'semesters',
                'gurus',
                'kelasAkademiks',
                'mataPelajarans'
            )
        );
    }

    public function update(
        Request $request,
        Mengajar $mengajar
    ): RedirectResponse {
        $validated = $request->validate([
            'semester_id' => [
                'required',
                'exists:semesters,id',
            ],
            'guru_id' => [
                'required',
                'exists:gurus,id',
            ],
            'kelas_akademik_id' => [
                'required',
                'exists:kelas_akademiks,id',
            ],
            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id',
            ],
        ]);

        $this->validatePenugasan(
            $validated,
            $mengajar->id
        );

        $contextChanged = (
            $mengajar->semester_id
                !== (int) $validated['semester_id']
            || $mengajar->kelas_akademik_id
                !== (int) $validated['kelas_akademik_id']
            || $mengajar->mata_pelajaran_id
                !== (int) $validated['mata_pelajaran_id']
        );

        if (
            $contextChanged
            && (
                $mengajar->pertemuans()->exists()
                || $mengajar->penilaians()->exists()
            )
        ) {
            throw ValidationException::withMessages([
                'semester_id' => 'Semester, kelas, atau mata pelajaran tidak dapat diubah karena penugasan sudah memiliki data pertemuan atau penilaian.',
            ]);
        }

        $mengajar->update($validated);

        return redirect()
            ->route('pegawai-tu.akademik.mengajar.index')
            ->with(
                'success',
                'Penugasan mengajar berhasil diperbarui.'
            );
    }

    public function destroy(
        Mengajar $mengajar
    ): RedirectResponse {
        if (
            $mengajar->jadwals()->exists()
            || $mengajar->pertemuans()->exists()
            || $mengajar->penilaians()->exists()
        ) {
            return back()->with(
                'error',
                'Penugasan mengajar tidak dapat dihapus karena sudah memiliki jadwal atau data akademik terkait.'
            );
        }

        $mengajar->delete();

        return redirect()
            ->route('pegawai-tu.akademik.mengajar.index')
            ->with(
                'success',
                'Penugasan mengajar berhasil dihapus.'
            );
    }

    private function validatePenugasan(
        array $validated,
        ?int $exceptMengajarId = null
    ): void {
        $semester = Semester::with('tahunAkademik')
            ->findOrFail(
                $validated['semester_id']
            );

        $kelasAkademik = KelasAkademik::with(
            'tahunAkademik'
        )
            ->findOrFail(
                $validated['kelas_akademik_id']
            );

        $guru = Guru::with('user')
            ->findOrFail(
                $validated['guru_id']
            );

        $mataPelajaran = MataPelajaran::findOrFail(
            $validated['mata_pelajaran_id']
        );

        if (
            $semester->tahun_akademik_id
            !== $kelasAkademik->tahun_akademik_id
        ) {
            throw ValidationException::withMessages([
                'kelas_akademik_id' => 'Kelas akademik harus berasal dari tahun akademik yang sama dengan semester.',
            ]);
        }

        if (
            $guru->status !== 'aktif'
            || ! $guru->user?->is_active
        ) {
            throw ValidationException::withMessages([
                'guru_id' => 'Guru yang dipilih tidak aktif.',
            ]);
        }

        if (! $mataPelajaran->is_active) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran yang dipilih tidak aktif.',
            ]);
        }

        $query = Mengajar::where(
            'semester_id',
            $validated['semester_id']
        )
            ->where(
                'guru_id',
                $validated['guru_id']
            )
            ->where(
                'kelas_akademik_id',
                $validated['kelas_akademik_id']
            )
            ->where(
                'mata_pelajaran_id',
                $validated['mata_pelajaran_id']
            );

        if ($exceptMengajarId) {
            $query->whereKeyNot($exceptMengajarId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Penugasan mengajar yang sama sudah tersedia.',
            ]);
        }
    }
}