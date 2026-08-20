<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(Request $request): View
    {
        $semesters = Semester::with('tahunAkademik')
            ->when(
                $request->filled('tahun_akademik_id'),
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $request->integer('tahun_akademik_id')
                )
            )
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->withQueryString();

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        return view(
            'master.semester.index',
            compact('semesters', 'tahunAkademiks')
        );
    }

    public function create(): View
    {
        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        return view(
            'master.semester.create',
            compact('tahunAkademiks')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_akademik_id' => [
                'required',
                'exists:tahun_akademiks,id',
            ],
            'nama' => [
                'required',
                Rule::in([
                    'ganjil',
                    'genap',
                ]),
            ],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'is_rapor_open' => [
                'nullable',
                'boolean',
            ],
            'tanggal_rapor' => [
                'nullable',
                'date',
            ],
        ]);

        $exists = Semester::where(
            'tahun_akademik_id',
            $validated['tahun_akademik_id']
        )
            ->where('nama', $validated['nama'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama' => 'Semester tersebut sudah tersedia pada tahun akademik yang dipilih.',
            ]);
        }

        $this->validateSemesterDates($validated);

        DB::transaction(function () use ($validated): void {
            $isActive = (bool) ($validated['is_active'] ?? false);

            if ($isActive) {
                Semester::query()->update([
                    'is_active' => false,
                ]);
            }

            Semester::create([
                ...$validated,
                'is_active' => $isActive,
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function show(Semester $semester): View
    {
        $semester->load('tahunAkademik');

        return view(
            'master.semester.show',
            compact('semester')
        );
    }

    public function edit(Semester $semester): View
    {
        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        return view(
            'master.semester.edit',
            compact('semester', 'tahunAkademiks')
        );
    }

    public function update(
        Request $request,
        Semester $semester
    ): RedirectResponse {
        $validated = $request->validate([
            'tahun_akademik_id' => [
                'required',
                'exists:tahun_akademiks,id',
            ],
            'nama' => [
                'required',
                Rule::in([
                    'ganjil',
                    'genap',
                ]),
            ],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'is_rapor_open' => [
                'nullable',
                'boolean',
            ],
            'tanggal_rapor' => [
                'nullable',
                'date',
            ],
        ]);

        $exists = Semester::where(
            'tahun_akademik_id',
            $validated['tahun_akademik_id']
        )
            ->where('nama', $validated['nama'])
            ->whereKeyNot($semester->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama' => 'Semester tersebut sudah tersedia pada tahun akademik yang dipilih.',
            ]);
        }

        $this->validateSemesterDates($validated);

        DB::transaction(function () use (
            $validated,
            $semester
        ): void {
            $isActive = (bool) ($validated['is_active'] ?? false);
            $isRaporOpen = (bool) ($validated['is_rapor_open'] ?? false);

            if (
                $semester->is_active
                && ! $isActive
                && ! Semester::whereKeyNot($semester->id)
                    ->where('is_active', true)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'is_active' => 'Semester aktif tidak dapat dinonaktifkan sebelum semester lain diaktifkan.',
                ]);
            }

            if ($isActive) {
                Semester::whereKeyNot($semester->id)
                    ->update([
                        'is_active' => false,
                    ]);
            }

            $semester->update([
                ...$validated,
                'is_active' => $isActive,
                'is_rapor_open' => $isRaporOpen,
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function toggleRapor(Request $request, Semester $semester): RedirectResponse
    {
        $status = ! $semester->is_rapor_open;

        $semester->update([
            'is_rapor_open' => $status,
            'tanggal_rapor' => $request->input('tanggal_rapor', $semester->tanggal_rapor ?? now()),
        ]);

        $pesan = $status
            ? 'Akses cetak rapor untuk ' . ucfirst($semester->nama) . ' berhasil DIBUKA.'
            : 'Akses cetak rapor untuk ' . ucfirst($semester->nama) . ' berhasil DIKUNCI / DITUTUP.';

        return back()->with('success', $pesan);
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        if ($semester->is_active) {
            return back()->with(
                'error',
                'Semester aktif tidak dapat dihapus.'
            );
        }

        if ($semester->mengajars()->exists()) {
            return back()->with(
                'error',
                'Semester tidak dapat dihapus karena sudah digunakan pada data mengajar.'
            );
        }

        $semester->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Semester berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.semester.{$action}";
    }

    private function validateSemesterDates(array $validated): void
    {
        $tahunAkademik = TahunAkademik::findOrFail(
            $validated['tahun_akademik_id']
        );

        if (
            $validated['tanggal_mulai']
                < $tahunAkademik->tanggal_mulai?->format('Y-m-d')
            || $validated['tanggal_selesai']
                > $tahunAkademik->tanggal_selesai?->format('Y-m-d')
        ) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Periode semester harus berada dalam periode tahun akademik.',
            ]);
        }
    }
}
