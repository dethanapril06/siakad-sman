<?php

namespace App\Http\Controllers\Akademik;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\KelasAkademik;
use App\Models\Mengajar;
use App\Models\Ruangan;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $semesterId = $request->integer('semester_id');

        if (! $semesterId) {
            $semesterId = Semester::aktif()->value('id');
        }

        $jadwals = Jadwal::with([
            'mengajar.semester.tahunAkademik',
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereHas(
                'mengajar',
                function ($query) use ($semesterId) {
                    if ($semesterId) {
                        $query->where(
                            'semester_id',
                            $semesterId
                        );
                    }
                }
            )
            ->when(
                $request->filled('kelas_akademik_id'),
                fn ($query) => $query->whereHas(
                    'mengajar',
                    fn ($query) => $query->where(
                        'kelas_akademik_id',
                        $request->integer('kelas_akademik_id')
                    )
                )
            )
            ->when(
                $request->filled('hari'),
                fn ($query) => $query->where(
                    'hari',
                    $request->string('hari')->toString()
                )
            )
            ->when(
                $request->filled('guru_id'),
                fn ($query) => $query->whereHas(
                    'mengajar',
                    fn ($query) => $query->where(
                        'guru_id',
                        $request->integer('guru_id')
                    )
                )
            )
            ->orderByRaw("
                CASE hari
                    WHEN 'senin' THEN 1
                    WHEN 'selasa' THEN 2
                    WHEN 'rabu' THEN 3
                    WHEN 'kamis' THEN 4
                    WHEN 'jumat' THEN 5
                    WHEN 'sabtu' THEN 6
                    ELSE 7
                END
            ")
            ->orderBy('jam_mulai')
                ->paginate(10)
                ->withQueryString();

        $semesters = Semester::with('tahunAkademik')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])->get();

        return view(
            'akademik.jadwal.index',
            compact(
                'jadwals',
                'semesters',
                'kelasAkademiks',
                'semesterId'
            )
        );
    }

    public function create(): View
    {
        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'guru',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->whereHas(
                'semester',
                fn ($query) => $query->where(
                    'is_active',
                    true
                )
            )
            ->whereDoesntHave('jadwals')
            ->get();

        $ruangans = Ruangan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.jadwal.create',
            compact(
                'mengajars',
                'ruangans'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
                'unique:jadwals,mengajar_id',
            ],
            'ruangan_id' => [
                'nullable',
                'exists:ruangans,id',
            ],
            'hari' => [
                'required',
                Rule::in([
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat',
                    'sabtu',
                ]),
            ],
            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],
            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],
        ], [
            'mengajar_id.required' => 'Penugasan mengajar wajib dipilih.',
            'mengajar_id.unique' => 'Penugasan mengajar yang dipilih sudah memiliki jadwal pembelajaran.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $this->validateBentrok($validated);

        Jadwal::create($validated);

        return redirect()
            ->route('pegawai-tu.akademik.jadwal.index')
            ->with(
                'success',
                'Jadwal pembelajaran berhasil ditambahkan.'
            );
    }

    public function show(Jadwal $jadwal): View
    {
        $jadwal->load([
            'mengajar.semester.tahunAkademik',
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ]);

        return view(
            'akademik.jadwal.show',
            compact('jadwal')
        );
    }

    public function edit(Jadwal $jadwal): View
    {
        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'guru',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->whereHas(
                'semester',
                fn ($query) => $query->where(
                    'is_active',
                    true
                )
            )
            ->where(function ($query) use ($jadwal) {
                $query->whereDoesntHave('jadwals')
                    ->orWhere('id', $jadwal->mengajar_id);
            })
            ->get();

        $ruangans = Ruangan::aktif()
            ->orderBy('nama')
            ->get();

        return view(
            'akademik.jadwal.edit',
            compact(
                'jadwal',
                'mengajars',
                'ruangans'
            )
        );
    }

    public function update(
        Request $request,
        Jadwal $jadwal
    ): RedirectResponse {
        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
                Rule::unique('jadwals', 'mengajar_id')->ignore($jadwal->id),
            ],
            'ruangan_id' => [
                'nullable',
                'exists:ruangans,id',
            ],
            'hari' => [
                'required',
                Rule::in([
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat',
                    'sabtu',
                ]),
            ],
            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],
            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],
        ]);

        $this->validateBentrok(
            $validated,
            $jadwal->id
        );

        $jadwal->update($validated);

        return redirect()
            ->route('pegawai-tu.akademik.jadwal.index')
            ->with(
                'success',
                'Jadwal pembelajaran berhasil diperbarui.'
            );
    }

    public function destroy(
        Jadwal $jadwal
    ): RedirectResponse {
        $jadwal->delete();

        return redirect()
            ->route('pegawai-tu.akademik.jadwal.index')
            ->with(
                'success',
                'Jadwal pembelajaran berhasil dihapus.'
            );
    }

    private function validateBentrok(
        array $validated,
        ?int $exceptJadwalId = null
    ): void {
        $mengajar = Mengajar::with([
            'semester',
            'guru',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
        ])
            ->findOrFail(
                $validated['mengajar_id']
            );

        if (! $mengajar->semester->is_active) {
            throw ValidationException::withMessages([
                'mengajar_id' => 'Jadwal hanya dapat dibuat untuk semester aktif.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Duplikasi Mata Pelajaran per Kelas dalam 1 Minggu / Semester
        |--------------------------------------------------------------------------
        */

        $existingJadwalMapel = Jadwal::with(['mengajar.guru', 'mengajar.mataPelajaran', 'mengajar.kelasAkademik'])
            ->whereHas('mengajar', function ($query) use ($mengajar) {
                $query->where('kelas_akademik_id', $mengajar->kelas_akademik_id)
                    ->where('mata_pelajaran_id', $mengajar->mata_pelajaran_id)
                    ->where('semester_id', $mengajar->semester_id);
            })
            ->when($exceptJadwalId, fn ($query) => $query->whereKeyNot($exceptJadwalId))
            ->first();

        if ($existingJadwalMapel) {
            $mapelNama = $mengajar->mataPelajaran?->nama ?? 'Mata pelajaran';
            $kelasNama = $mengajar->kelasAkademik?->nama_lengkap ?? 'kelas ini';
            $hariNama = ucfirst($existingJadwalMapel->hari);
            $jamSlot = $existingJadwalMapel->jam;

            throw ValidationException::withMessages([
                'mengajar_id' => "Mata pelajaran {$mapelNama} sudah memiliki jadwal di kelas {$kelasNama} pada hari {$hariNama} ({$jamSlot}). Setiap mata pelajaran pada kelas yang sama hanya dapat dijadwalkan 1 kali dalam seminggu.",
            ]);
        }

        if (
            $validated['ruangan_id'] ?? null
        ) {
            $ruangan = Ruangan::findOrFail(
                $validated['ruangan_id']
            );

            if (! $ruangan->is_active) {
                throw ValidationException::withMessages([
                    'ruangan_id' => 'Ruangan yang dipilih tidak aktif.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Query Jadwal yang Waktunya Beririsan
        |--------------------------------------------------------------------------
        */

        $bentrokQuery = Jadwal::with([
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->where('hari', $validated['hari'])
            ->where(
                'jam_mulai',
                '<',
                $validated['jam_selesai']
            )
            ->where(
                'jam_selesai',
                '>',
                $validated['jam_mulai']
            );

        if ($exceptJadwalId) {
            $bentrokQuery->whereKeyNot(
                $exceptJadwalId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Bentrok Guru
        |--------------------------------------------------------------------------
        */

        $bentrokGuru = (clone $bentrokQuery)
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'guru_id',
                    $mengajar->guru_id
                )
            )
            ->first();

        if ($bentrokGuru) {
            $guru = $mengajar->guru->nama;

            $kelasBentrok = $bentrokGuru
                ->mengajar
                ->kelasAkademik
                ->nama_lengkap;

            $mataPelajaranBentrok = $bentrokGuru
                ->mengajar
                ->mataPelajaran
                ->nama;

            throw ValidationException::withMessages([
                'jam_mulai' => "Guru {$guru} sudah memiliki jadwal {$mataPelajaranBentrok} di kelas {$kelasBentrok} pada {$bentrokGuru->jam}.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Bentrok Kelas
        |--------------------------------------------------------------------------
        */

        $bentrokKelas = (clone $bentrokQuery)
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'kelas_akademik_id',
                    $mengajar->kelas_akademik_id
                )
            )
            ->first();

        if ($bentrokKelas) {
            $kelas = $mengajar
                ->kelasAkademik
                ->nama_lengkap;

            $mataPelajaranBentrok = $bentrokKelas
                ->mengajar
                ->mataPelajaran
                ->nama;

            $guruBentrok = $bentrokKelas
                ->mengajar
                ->guru
                ->nama;

            throw ValidationException::withMessages([
                'jam_mulai' => "Kelas {$kelas} sudah memiliki jadwal {$mataPelajaranBentrok} bersama {$guruBentrok} pada {$bentrokKelas->jam}.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Bentrok Ruangan
        |--------------------------------------------------------------------------
        */

        if ($validated['ruangan_id'] ?? null) {
            $bentrokRuangan = (clone $bentrokQuery)
                ->where(
                    'ruangan_id',
                    $validated['ruangan_id']
                )
                ->first();

            if ($bentrokRuangan) {
                $ruangan = $bentrokRuangan
                    ->ruangan
                    ->nama;

                $kelasBentrok = $bentrokRuangan
                    ->mengajar
                    ->kelasAkademik
                    ->nama_lengkap;

                throw ValidationException::withMessages([
                    'ruangan_id' => "Ruangan {$ruangan} sedang digunakan oleh kelas {$kelasBentrok} pada {$bentrokRuangan->jam}.",
                ]);
            }
        }
    }
}