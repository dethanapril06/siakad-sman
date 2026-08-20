<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\KelasAkademik;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $siswas = Siswa::with([
            'user',
            'anggotaKelas' => function ($query) {
                $query->whereHas(
                    'kelasAkademik.tahunAkademik',
                    fn ($query) => $query->where(
                        'is_active',
                        true
                    )
                );
            },
            'anggotaKelas.kelasAkademik.kelas.jurusan',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->toString()
                )
            )
            ->when(
                $request->filled('kelas_akademik_id'),
                fn ($query) => $query->whereHas(
                    'anggotaKelas',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $request->integer('kelas_akademik_id')
                    )
                )
            )
            ->when(
                $request->filled('jurusan_id'),
                fn ($query) => $query->whereHas(
                    'anggotaKelas.kelasAkademik.kelas',
                    fn ($query) => $query->where(
                        'jurusan_id',
                        $request->integer('jurusan_id')
                    )
                )
            )
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])
            ->whereHas(
                'tahunAkademik',
                fn ($query) => $query->where('is_active', true)
            )
            ->get();

        $jurusans = Jurusan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'master.siswa.index',
            compact(
                'siswas',
                'kelasAkademiks',
                'jurusans'
            )
        );
    }

    public function create(): View
    {
        return view('master.siswa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => [
                'required',
                'string',
                'max:30',
                'unique:siswas,nis',
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:30',
                'unique:siswas,nisn',
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
            'nama_orang_tua' => [
                'nullable',
                'string',
                'max:150',
            ],
            'no_hp_orang_tua' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'lulus',
                    'pindah',
                    'nonaktif',
                ]),
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $roleSiswa = Role::where(
                'name',
                'siswa'
            )->firstOrFail();

            $user = User::create([
                'role_id' => $roleSiswa->id,
                'name' => $validated['nama'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'is_active' => $validated['status'] === 'aktif',
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'nama_orang_tua' => $validated['nama_orang_tua'] ?? null,
                'no_hp_orang_tua' => $validated['no_hp_orang_tua'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load([
            'user',
            'anggotaKelas.kelasAkademik.kelas.jurusan',
            'anggotaKelas.kelasAkademik.tahunAkademik',
        ]);

        return view(
            'master.siswa.show',
            compact('siswa')
        );
    }

    public function edit(Siswa $siswa): View
    {
        $siswa->load('user');

        return view(
            'master.siswa.edit',
            compact('siswa')
        );
    }

    public function update(
        Request $request,
        Siswa $siswa
    ): RedirectResponse {
        $validated = $request->validate([
            'nis' => [
                'required',
                'string',
                'max:30',
                Rule::unique('siswas', 'nis')
                    ->ignore($siswa->id),
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('siswas', 'nisn')
                    ->ignore($siswa->id),
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
                    ->ignore($siswa->user_id),
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
            'nama_orang_tua' => [
                'nullable',
                'string',
                'max:150',
            ],
            'no_hp_orang_tua' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'lulus',
                    'pindah',
                    'nonaktif',
                ]),
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $siswa
        ): void {
            $userData = [
                'name' => $validated['nama'],
                'email' => strtolower($validated['email']),
                'is_active' => $validated['status'] === 'aktif',
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $siswa->user->update($userData);

            $siswa->update([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'nama_orang_tua' => $validated['nama_orang_tua'] ?? null,
                'no_hp_orang_tua' => $validated['no_hp_orang_tua'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        if (
            $siswa->anggotaKelas()->exists()
            || $siswa->absensis()->exists()
            || $siswa->nilais()->exists()
        ) {
            return back()->with(
                'error',
                'Siswa tidak dapat dihapus karena sudah memiliki data akademik. Ubah status siswa sebagai gantinya.'
            );
        }

        DB::transaction(function () use ($siswa): void {
            $user = $siswa->user;

            $siswa->delete();

            $user?->delete();
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function resetPassword(Siswa $siswa): RedirectResponse
    {
        abort_if(! $siswa->user, 404, 'Akun user untuk siswa ini tidak ditemukan.');

        $siswa->user->update([
            'password' => 'password',
        ]);

        return back()->with(
            'success',
            'Password akun siswa ' . $siswa->nama . ' berhasil direset ke password default (password).'
        );
    }

    private function routeName(string $action): string
    {
        $prefix = auth()->user()?->isKepalaSekolah()
            ? 'kepala-sekolah'
            : 'pegawai-tu';

        return "{$prefix}.master.siswa.{$action}";
    }
}
