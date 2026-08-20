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

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::orderBy('name')->get();

        $users = User::with([
            'role',
            'pegawaiTu',
            'guru',
            'siswa',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role_id'), function ($query) use ($request) {
                $query->where('role_id', $request->integer('role_id'));
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->input('is_active') === '1');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'master.user.index',
            compact('users', 'roles')
        );
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view(
            'master.user.create',
            compact('roles')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
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
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role_id' => $validated['role_id'],
                'is_active' => (bool) $validated['is_active'],
            ]);

            $role = Role::find($validated['role_id']);

            // Jika role adalah pegawai_tu, inisialisasi profil PegawaiTu dasar jika belum ada
            if ($role && $role->name === 'pegawai_tu') {
                PegawaiTu::create([
                    'user_id' => $user->id,
                    'nip' => '-',
                    'nama' => $user->name,
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => null,
                    'tanggal_lahir' => null,
                    'alamat' => null,
                    'no_hp' => null,
                ]);
            }
        });

        return redirect()
            ->route('pegawai-tu.master.user.index')
            ->with('success', 'Data pengguna akun berhasil ditambahkan.');
    }

    public function show(User $user): View
    {
        $user->load([
            'role',
            'pegawaiTu',
            'guru',
            'siswa',
        ]);

        return view(
            'master.user.show',
            compact('user')
        );
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view(
            'master.user.edit',
            compact('user', 'roles')
        );
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8),
                'confirmed',
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        // Proteksi jika menonaktifkan akun sendiri
        if (Auth::id() === $user->id && ! (bool) $validated['is_active']) {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        DB::transaction(function () use ($user, $validated) {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
                'is_active' => (bool) $validated['is_active'],
            ];

            if (! empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            $user->update($updateData);

            // Sinkronisasi nama pada relasi profil yang ada
            if ($user->pegawaiTu) {
                $user->pegawaiTu->update(['nama' => $validated['name']]);
            }
            if ($user->guru) {
                $user->guru->update(['nama' => $validated['name']]);
            }
            if ($user->siswa) {
                $user->siswa->update(['nama' => $validated['name']]);
            }
        });

        return redirect()
            ->route('pegawai-tu.master.user.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Proteksi: Tidak boleh menghapus akun yang sedang aktif digunakan
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            $user->pegawaiTu()?->delete();
            $user->guru()?->delete();
            $user->siswa()?->delete();
            $user->delete();
        });

        return redirect()
            ->route('pegawai-tu.master.user.index')
            ->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update([
            'password' => 'password',
        ]);

        return back()->with(
            'success',
            'Password pengguna ' . $user->name . ' (' . ($user->role?->name ?? 'User') . ') berhasil direset ke password default (password).'
        );
    }
}
