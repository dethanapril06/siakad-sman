<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PegawaiTu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PegawaiTuController extends Controller
{
    public function index(Request $request): View
    {
        $pegawaiTus = PegawaiTu::with([
            'user',
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
                $request->filled('jenis_kelamin'),
                fn ($query) => $query->where(
                    'jenis_kelamin',
                    $request->string('jenis_kelamin')->toString()
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.pegawai-tu.index',
            compact('pegawaiTus')
        );
    }

    public function create(): View
    {
        return view('master.pegawai-tu.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => [
                'required',
                'string',
                'max:30',
                'unique:pegawai_tus,nip',
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
                Password::min(8),
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
        ]);

        DB::transaction(function () use ($validated): void {
            $roleTu = Role::where(
                'name',
                'pegawai_tu'
            )->firstOrFail();

            $user = User::create([
                'role_id' => $roleTu->id,
                'name' => $validated['nama'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'is_active' => true,
            ]);

            PegawaiTu::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()
            ->route('pegawai-tu.master.pegawai-tu.index')
            ->with('success', 'Data Pegawai TU berhasil ditambahkan.');
    }

    public function show(PegawaiTu $pegawaiTu): View
    {
        $pegawaiTu->load('user');

        return view(
            'master.pegawai-tu.show',
            compact('pegawaiTu')
        );
    }

    public function edit(PegawaiTu $pegawaiTu): View
    {
        $pegawaiTu->load('user');

        return view(
            'master.pegawai-tu.edit',
            compact('pegawaiTu')
        );
    }

    public function update(
        Request $request,
        PegawaiTu $pegawaiTu
    ): RedirectResponse {
        $user = $pegawaiTu->user;

        $validated = $request->validate([
            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('pegawai_tus', 'nip')->ignore($pegawaiTu->id),
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
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8),
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
        ]);

        DB::transaction(function () use (
            $pegawaiTu,
            $user,
            $validated
        ): void {
            if ($user) {
                $userUpdate = [
                    'name' => $validated['nama'],
                    'email' => strtolower($validated['email']),
                ];

                if (! empty($validated['password'])) {
                    $userUpdate['password'] = $validated['password'];
                }

                $user->update($userUpdate);
            }

            $pegawaiTu->update([
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()
            ->route('pegawai-tu.master.pegawai-tu.index')
            ->with('success', 'Data Pegawai TU berhasil diperbarui.');
    }

    public function destroy(PegawaiTu $pegawaiTu): RedirectResponse
    {
        // Proteksi: tidak dapat menghapus akun diri sendiri yang sedang login
        if (Auth::id() === $pegawaiTu->user_id) {
            return back()->with('error', 'Anda tidak dapat menghapus data akun Pegawai TU Anda sendiri.');
        }

        DB::transaction(function () use ($pegawaiTu): void {
            $user = $pegawaiTu->user;

            $pegawaiTu->delete();

            $user?->delete();
        });

        return redirect()
            ->route('pegawai-tu.master.pegawai-tu.index')
            ->with('success', 'Data Pegawai TU berhasil dihapus.');
    }

    public function resetPassword(PegawaiTu $pegawaiTu): RedirectResponse
    {
        abort_if(! $pegawaiTu->user, 404, 'Akun user untuk Pegawai TU ini tidak ditemukan.');

        $pegawaiTu->user->update([
            'password' => 'password',
        ]);

        return back()->with(
            'success',
            'Password akun Pegawai TU ' . $pegawaiTu->nama . ' berhasil direset ke password default (password).'
        );
    }
}
