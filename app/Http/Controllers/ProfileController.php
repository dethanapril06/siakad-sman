<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function pegawaiTu(): View
    {
        return $this->show('layouts.pegawai-tu', 'pegawai_tu', 'Pegawai TU', Auth::user()->pegawaiTu);
    }

    public function guru(): View
    {
        return $this->show('layouts.guru', 'guru', 'Guru', Auth::user()->guru);
    }

    public function siswa(): View
    {
        return $this->show('layouts.siswa', 'siswa', 'Siswa', Auth::user()->siswa);
    }

    public function kepalaSekolah(): View
    {
        return $this->show('layouts.kepala-sekolah', 'kepala_sekolah', 'Kepala Sekolah', Auth::user()->guru);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $roleName = $user->role?->name;
        $profile = $this->profileFor($user, $roleName);

        if (! $profile) {
            return back()->withErrors([
                'profile' => 'Data profil pribadi belum tersedia.',
            ]);
        }

        $validated = $request->validate($this->rules($user, $roleName), $this->messages());

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $user->update($userData);

        $profile->update($this->personalData($validated, $roleName));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    private function show(string $layout, string $roleName, string $roleLabel, mixed $profile): View
    {
        return view('profile.edit', [
            'layout' => $layout,
            'roleName' => $roleName,
            'roleLabel' => $roleLabel,
            'profile' => $profile,
        ]);
    }

    private function profileFor(User $user, ?string $roleName): mixed
    {
        return match ($roleName) {
            'pegawai_tu' => $user->pegawaiTu,
            'guru', 'kepala_sekolah' => $user->guru,
            'siswa' => $user->siswa,
            default => null,
        };
    }

    private function rules(User $user, ?string $roleName): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
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
                'max:255',
            ],
            'tanggal_lahir' => [
                'nullable',
                'date',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
        ];

        if (in_array($roleName, [
            'pegawai_tu',
            'guru',
            'kepala_sekolah',
        ], true)) {
            $rules['no_hp'] = [
                'nullable',
                'string',
                'max:20',
            ];
        }

        if ($roleName === 'siswa') {
            $rules['nama_orang_tua'] = [
                'nullable',
                'string',
                'max:255',
            ];
            $rules['no_hp_orang_tua'] = [
                'nullable',
                'string',
                'max:20',
            ];
        }

        return $rules;
    }

    private function personalData(array $validated, ?string $roleName): array
    {
        $data = [
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        if (in_array($roleName, [
            'pegawai_tu',
            'guru',
            'kepala_sekolah',
        ], true)) {
            $data['no_hp'] = $validated['no_hp'] ?? null;
        }

        if ($roleName === 'siswa') {
            $data['nama_orang_tua'] = $validated['nama_orang_tua'] ?? null;
            $data['no_hp_orang_tua'] = $validated['no_hp_orang_tua'] ?? null;
        }

        return $data;
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama akun wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
        ];
    }
}
