<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RuanganController extends Controller
{
    public function index(Request $request): View
    {
        $ruangans = Ruangan::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('jenis'),
                fn ($query) => $query->where(
                    'jenis',
                    $request->string('jenis')->toString()
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.ruangan.index',
            compact('ruangans')
        );
    }

    public function create(): View
    {
        return view('master.ruangan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                'unique:ruangans,kode',
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'jenis' => [
                'required',
                Rule::in([
                    'kelas',
                    'laboratorium',
                    'lainnya',
                ]),
            ],
            'kapasitas' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        Ruangan::create([
            ...$validated,
            'kode' => strtoupper($validated['kode']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function show(Ruangan $ruangan): View
    {
        return view(
            'master.ruangan.show',
            compact('ruangan')
        );
    }

    public function edit(Ruangan $ruangan): View
    {
        return view(
            'master.ruangan.edit',
            compact('ruangan')
        );
    }

    public function update(
        Request $request,
        Ruangan $ruangan
    ): RedirectResponse {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ruangans', 'kode')
                    ->ignore($ruangan->id),
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'jenis' => [
                'required',
                Rule::in([
                    'kelas',
                    'laboratorium',
                    'lainnya',
                ]),
            ],
            'kapasitas' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $ruangan->update([
            ...$validated,
            'kode' => strtoupper($validated['kode']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan): RedirectResponse
    {
        if ($ruangan->jadwals()->exists()) {
            return back()->with(
                'error',
                'Ruangan tidak dapat dihapus karena sudah digunakan pada jadwal.'
            );
        }

        $ruangan->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Ruangan berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.ruangan.{$action}";
    }
}
