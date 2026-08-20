<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $mataPelajarans = MataPelajaran::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('kelompok'),
                fn ($query) => $query->where(
                    'kelompok',
                    $request->string('kelompok')->toString()
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.mata-pelajaran.index',
            compact('mataPelajarans')
        );
    }

    public function create(): View
    {
        return view('master.mata-pelajaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                'unique:mata_pelajarans,kode',
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'kelompok' => [
                'nullable',
                'string',
                'max:100',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        MataPelajaran::create([
            'kode' => strtoupper($validated['kode']),
            'nama' => $validated['nama'],
            'kelompok' => $validated['kelompok'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran): View
    {
        return view(
            'master.mata-pelajaran.show',
            compact('mataPelajaran')
        );
    }

    public function edit(MataPelajaran $mataPelajaran): View
    {
        return view(
            'master.mata-pelajaran.edit',
            compact('mataPelajaran')
        );
    }

    public function update(
        Request $request,
        MataPelajaran $mataPelajaran
    ): RedirectResponse {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mata_pelajarans', 'kode')
                    ->ignore($mataPelajaran->id),
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'kelompok' => [
                'nullable',
                'string',
                'max:100',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $mataPelajaran->update([
            'kode' => strtoupper($validated['kode']),
            'nama' => $validated['nama'],
            'kelompok' => $validated['kelompok'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(
        MataPelajaran $mataPelajaran
    ): RedirectResponse {
        if ($mataPelajaran->mengajars()->exists()) {
            return back()->with(
                'error',
                'Mata pelajaran tidak dapat dihapus karena sudah digunakan pada data mengajar.'
            );
        }

        $mataPelajaran->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.mata-pelajaran.{$action}";
    }
}
