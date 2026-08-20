<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $gurus = Guru::with([
            'user.role',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('nip', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->toString()
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.guru.index',
            compact('gurus')
        );
    }

    public function create(): View
    {
        return view('master.guru.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => [
                'required',
                'string',
                'max:30',
                'unique:gurus,nip',
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in([
                    'L',
                    'P',
                ]),
            ],
            'tempat_lahir' => [
                'nullable',
                'string',
                'max:100',
            ],
            'tanggal_lahir' => [
                'nullable',
                'date',
                'before:today',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'nonaktif',
                ]),
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $roleGuru = Role::where(
                'name',
                'guru'
            )->firstOrFail();

            $user = User::create([
                'role_id' => $roleGuru->id,
                'name' => $validated['nama'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'is_active' => $validated['status'] === 'aktif',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load([
            'user.role',
            'kelasWali.kelas.jurusan',
            'kelasWali.tahunAkademik',
        ]);

        return view(
            'master.guru.show',
            compact('guru')
        );
    }

    public function edit(Guru $guru): View
    {
        $guru->load('user');

        return view(
            'master.guru.edit',
            compact('guru')
        );
    }

    public function update(
        Request $request,
        Guru $guru
    ): RedirectResponse {
        $validated = $request->validate([
            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('gurus', 'nip')
                    ->ignore($guru->id),
            ],
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($guru->user_id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in([
                    'L',
                    'P',
                ]),
            ],
            'tempat_lahir' => [
                'nullable',
                'string',
                'max:100',
            ],
            'tanggal_lahir' => [
                'nullable',
                'date',
                'before:today',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'nonaktif',
                ]),
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $guru
        ): void {
            $userData = [
                'name' => $validated['nama'],
                'email' => strtolower($validated['email']),
                'is_active' => $validated['status'] === 'aktif',
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $guru->user->update($userData);

            $guru->update([
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        if ($guru->isKepalaSekolah()) {
            return back()->with(
                'error',
                'Data Kepala Sekolah tidak dapat dihapus melalui modul Guru.'
            );
        }

        if (
            $guru->kelasWali()->exists()
            || $guru->mengajars()->exists()
        ) {
            return back()->with(
                'error',
                'Guru tidak dapat dihapus karena sudah memiliki data akademik terkait. Nonaktifkan akun guru sebagai gantinya.'
            );
        }

        DB::transaction(function () use ($guru): void {
            $user = $guru->user;

            $guru->delete();

            $user?->delete();
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data guru berhasil dihapus.');
    }

    public function resetPassword(Guru $guru): RedirectResponse
    {
        abort_if(! $guru->user, 404, 'Akun user untuk guru ini tidak ditemukan.');

        $guru->user->update([
            'password' => 'password',
        ]);

        return back()->with(
            'success',
            'Password akun guru ' . $guru->nama . ' berhasil direset ke password default (password).'
        );
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.guru.{$action}";
    }
}
