<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $kelas = Kelas::with('jurusan')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('tingkat', 'like', "%{$search}%")
                        ->orWhereHas('jurusan', function ($query) use ($search) {
                            $query->where('kode', 'like', "%{$search}%")
                                ->orWhere('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $request->filled('jurusan_id'),
                fn ($query) => $query->where(
                    'jurusan_id',
                    $request->integer('jurusan_id')
                )
            )
            ->when(
                $request->filled('tingkat'),
                fn ($query) => $query->where(
                    'tingkat',
                    $request->string('tingkat')->toString()
                )
            )
            ->orderByRaw("
                CASE tingkat
                    WHEN 'X' THEN 1
                    WHEN 'XI' THEN 2
                    WHEN 'XII' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();

        $jurusans = Jurusan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'master.kelas.index',
            compact('kelas', 'jurusans')
        );
    }

    public function create(): View
    {
        $jurusans = Jurusan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'master.kelas.create',
            compact('jurusans')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jurusan_id' => [
                'nullable',
                'exists:jurusans,id',
            ],
            'tingkat' => [
                'required',
                Rule::in([
                    'X',
                    'XI',
                    'XII',
                ]),
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $exists = Kelas::where(
            'jurusan_id',
            $validated['jurusan_id'] ?? null
        )
            ->where('tingkat', $validated['tingkat'])
            ->where('nama', $validated['nama'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama' => 'Kelas dengan tingkat, jurusan, dan nama tersebut sudah tersedia.',
            ]);
        }

        Kelas::create([
            'jurusan_id' => $validated['jurusan_id'] ?? null,
            'tingkat' => $validated['tingkat'],
            'nama' => $validated['nama'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas): View
    {
        $kelas->load('jurusan');

        return view(
            'master.kelas.show',
            compact('kelas')
        );
    }

    public function edit(Kelas $kelas): View
    {
        $jurusans = Jurusan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'master.kelas.edit',
            compact('kelas', 'jurusans')
        );
    }

    public function update(
        Request $request,
        Kelas $kelas
    ): RedirectResponse {
        $validated = $request->validate([
            'jurusan_id' => [
                'nullable',
                'exists:jurusans,id',
            ],
            'tingkat' => [
                'required',
                Rule::in([
                    'X',
                    'XI',
                    'XII',
                ]),
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $exists = Kelas::where(
            'jurusan_id',
            $validated['jurusan_id'] ?? null
        )
            ->where('tingkat', $validated['tingkat'])
            ->where('nama', $validated['nama'])
            ->whereKeyNot($kelas->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama' => 'Kelas dengan tingkat, jurusan, dan nama tersebut sudah tersedia.',
            ]);
        }

        $kelas->update([
            'jurusan_id' => $validated['jurusan_id'] ?? null,
            'tingkat' => $validated['tingkat'],
            'nama' => $validated['nama'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->kelasAkademiks()->exists()) {
            return back()->with(
                'error',
                'Kelas tidak dapat dihapus karena sudah digunakan pada data kelas akademik.'
            );
        }

        $kelas->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.kelas.{$action}";
    }
}
