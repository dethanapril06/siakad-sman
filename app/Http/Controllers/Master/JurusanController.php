<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $jurusans = Jurusan::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.jurusan.index',
            compact('jurusans')
        );
    }

    public function create(): View
    {
        return view('master.jurusan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                'unique:jurusans,kode',
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        Jurusan::create([
            'kode' => strtoupper($validated['kode']),
            'nama' => $validated['nama'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function show(Jurusan $jurusan): View
    {
        return view(
            'master.jurusan.show',
            compact('jurusan')
        );
    }

    public function edit(Jurusan $jurusan): View
    {
        return view(
            'master.jurusan.edit',
            compact('jurusan')
        );
    }

    public function update(
        Request $request,
        Jurusan $jurusan
    ): RedirectResponse {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('jurusans', 'kode')
                    ->ignore($jurusan->id),
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $jurusan->update([
            'kode' => strtoupper($validated['kode']),
            'nama' => $validated['nama'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        if ($jurusan->kelas()->exists()) {
            return back()->with(
                'error',
                'Jurusan tidak dapat dihapus karena sudah digunakan pada data kelas.'
            );
        }

        $jurusan->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Jurusan berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.jurusan.{$action}";
    }
}
