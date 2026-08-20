<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TahunAkademikController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAkademiks = TahunAkademik::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where('nama', 'like', "%{$search}%");
            })
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.tahun-akademik.index',
            compact('tahunAkademiks')
        );
    }

    public function create(): View
    {
        return view('master.tahun-akademik.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:20',
                'unique:tahun_akademiks,nama',
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
        ], [
            'nama.required' => 'Nama tahun akademik wajib diisi.',
            'nama.unique' => 'Tahun akademik sudah tersedia.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        DB::transaction(function () use ($validated): void {
            $isActive = (bool) ($validated['is_active'] ?? false);

            if ($isActive) {
                TahunAkademik::query()->update([
                    'is_active' => false,
                ]);
            }

            TahunAkademik::create([
                'nama' => $validated['nama'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => $isActive,
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function show(TahunAkademik $tahunAkademik): View
    {
        return view(
            'master.tahun-akademik.show',
            compact('tahunAkademik')
        );
    }

    public function edit(TahunAkademik $tahunAkademik): View
    {
        return view(
            'master.tahun-akademik.edit',
            compact('tahunAkademik')
        );
    }

    public function update(
        Request $request,
        TahunAkademik $tahunAkademik
    ): RedirectResponse {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_akademiks', 'nama')
                    ->ignore($tahunAkademik->id),
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
        ]);

        DB::transaction(function () use (
            $validated,
            $tahunAkademik
        ): void {
            $isActive = (bool) ($validated['is_active'] ?? false);

            if (
                $tahunAkademik->is_active
                && ! $isActive
                && ! TahunAkademik::whereKeyNot($tahunAkademik->id)
                    ->where('is_active', true)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'is_active' => 'Tahun akademik aktif tidak dapat dinonaktifkan sebelum tahun akademik lain diaktifkan.',
                ]);
            }

            if ($isActive) {
                TahunAkademik::whereKeyNot($tahunAkademik->id)
                    ->update([
                        'is_active' => false,
                    ]);
            }

            $tahunAkademik->update([
                'nama' => $validated['nama'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => $isActive,
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    public function destroy(
        TahunAkademik $tahunAkademik
    ): RedirectResponse {
        if ($tahunAkademik->is_active) {
            return back()->with(
                'error',
                'Tahun akademik aktif tidak dapat dihapus.'
            );
        }

        if (
            $tahunAkademik->semesters()->exists()
            || $tahunAkademik->kelasAkademiks()->exists()
        ) {
            return back()->with(
                'error',
                'Tahun akademik tidak dapat dihapus karena sudah digunakan.'
            );
        }

        $tahunAkademik->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.tahun-akademik.{$action}";
    }
}
