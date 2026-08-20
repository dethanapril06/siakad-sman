<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JenisNilai;
use App\Models\Mengajar;
use App\Models\Penilaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $penilaians = Penilaian::with([
            'jenisNilai',
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
        ])
            ->withCount('nilais')
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
            ->when(
                $request->filled('jenis_nilai_id'),
                fn ($query) => $query->where(
                    'jenis_nilai_id',
                    $request->integer('jenis_nilai_id')
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
            ->get();

        $jenisNilais = JenisNilai::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'guru.penilaian.index',
            compact(
                'penilaians',
                'mengajars',
                'jenisNilais'
            )
        );
    }

    public function create(Request $request): View
    {
        $guru = Auth::user()->guru;

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->where('guru_id', $guru->id)
            ->whereHas(
                'semester',
                fn ($query) => $query->where(
                    'is_active',
                    true
                )
            )
            ->get();

        $jenisNilais = JenisNilai::aktif()
            ->orderBy('nama')
            ->get();

        $selectedMengajarId = $request->integer(
            'mengajar_id'
        );

        return view(
            'guru.penilaian.create',
            compact(
                'mengajars',
                'jenisNilais',
                'selectedMengajarId'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
            ],
            'jenis_nilai_id' => [
                'required',
                'exists:jenis_nilais,id',
            ],
            'judul' => [
                'required',
                'string',
                'max:255',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $mengajar = $this->getMengajarMilikGuru(
            $validated['mengajar_id']
        );

        $this->validatePenilaian(
            $validated,
            $mengajar
        );

        Penilaian::create($validated);

        return redirect()
            ->route('guru.penilaian.index')
            ->with(
                'success',
                'Data penilaian berhasil ditambahkan.'
            );
    }

    public function show(
        Penilaian $penilaian
    ): View {
        $this->authorizePenilaian($penilaian);

        $penilaian->load([
            'jenisNilai',
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'nilais.siswa',
        ]);

        return view(
            'guru.penilaian.show',
            compact('penilaian')
        );
    }

    public function edit(
        Penilaian $penilaian
    ): View {
        $this->authorizePenilaian($penilaian);

        $guru = Auth::user()->guru;

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->where('guru_id', $guru->id)
            ->get();

        $jenisNilais = JenisNilai::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'guru.penilaian.edit',
            compact(
                'penilaian',
                'mengajars',
                'jenisNilais'
            )
        );
    }

    public function update(
        Request $request,
        Penilaian $penilaian
    ): RedirectResponse {
        $this->authorizePenilaian($penilaian);

        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
            ],
            'jenis_nilai_id' => [
                'required',
                'exists:jenis_nilais,id',
            ],
            'judul' => [
                'required',
                'string',
                'max:255',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $mengajar = $this->getMengajarMilikGuru(
            $validated['mengajar_id']
        );

        if (
            $penilaian->mengajar_id
                !== $mengajar->id
            && $penilaian->nilais()->exists()
        ) {
            throw ValidationException::withMessages([
                'mengajar_id' => 'Penugasan mengajar tidak dapat diubah karena penilaian sudah memiliki nilai siswa.',
            ]);
        }

        $this->validatePenilaian(
            $validated,
            $mengajar
        );

        $penilaian->update($validated);

        return redirect()
            ->route('guru.penilaian.index')
            ->with(
                'success',
                'Data penilaian berhasil diperbarui.'
            );
    }

    public function destroy(
        Penilaian $penilaian
    ): RedirectResponse {
        $this->authorizePenilaian($penilaian);

        if ($penilaian->nilais()->exists()) {
            return back()->with(
                'error',
                'Penilaian tidak dapat dihapus karena sudah memiliki nilai siswa.'
            );
        }

        $penilaian->delete();

        return redirect()
            ->route('guru.penilaian.index')
            ->with(
                'success',
                'Data penilaian berhasil dihapus.'
            );
    }

    private function getMengajarMilikGuru(
        int $mengajarId
    ): Mengajar {
        return Mengajar::with('semester')
            ->whereKey($mengajarId)
            ->where(
                'guru_id',
                Auth::user()->guru?->id
            )
            ->firstOrFail();
    }

    private function authorizePenilaian(
        Penilaian $penilaian
    ): void {
        abort_unless(
            $penilaian->mengajar?->guru_id
                === Auth::user()->guru?->id,
            403,
            'Anda tidak memiliki akses ke penilaian ini.'
        );
    }

    private function validatePenilaian(
        array $validated,
        Mengajar $mengajar
    ): void {
        if (! $mengajar->semester->is_active) {
            throw ValidationException::withMessages([
                'mengajar_id' => 'Penilaian hanya dapat dibuat pada semester aktif.',
            ]);
        }

        $jenisNilai = JenisNilai::findOrFail(
            $validated['jenis_nilai_id']
        );

        if (! $jenisNilai->is_active) {
            throw ValidationException::withMessages([
                'jenis_nilai_id' => 'Jenis nilai yang dipilih tidak aktif.',
            ]);
        }

        $tanggal = $validated['tanggal'];

        if (
            $tanggal
                < $mengajar->semester
                    ->tanggal_mulai
                    ->format('Y-m-d')
            || $tanggal
                > $mengajar->semester
                    ->tanggal_selesai
                    ->format('Y-m-d')
        ) {
            throw ValidationException::withMessages([
                'tanggal' => 'Tanggal penilaian harus berada dalam periode semester.',
            ]);
        }
    }
}