<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mengajar;
use App\Models\Pertemuan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PertemuanController extends Controller
{
    public const MAX_PERTEMUAN = 16;

    public function index(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $pertemuans = Pertemuan::with([
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
        ])
            ->withCount('absensis')
            ->whereHas(
                'mengajar',
                fn ($query) => $query->where(
                    'guru_id',
                    $guru->id
                )
            )
            ->when(
                $request->filled('mengajar_id'),
                fn ($query) => $query->where(
                    'mengajar_id',
                    $request->integer('mengajar_id')
                )
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('pertemuan_ke')
            ->paginate(10)
            ->withQueryString();

        $mengajars = Mengajar::with([
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'semester.tahunAkademik',
        ])
            ->where('guru_id', $guru->id)
            ->orderByDesc('semester_id')
            ->get();

        return view(
            'guru.pertemuan.index',
            compact(
                'pertemuans',
                'mengajars'
            )
        );
    }

    public function create(Request $request): View
    {
        $guru = Auth::user()->guru;

        abort_if(
            ! $guru,
            403,
            'Data guru tidak ditemukan.'
        );

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'jadwals.ruangan',
        ])
            ->where('guru_id', $guru->id)
            ->whereHas(
                'semester',
                fn ($query) => $query->where(
                    'is_active',
                    true
                )
            )
            ->get();

        $selectedMengajarId = $request->integer(
            'mengajar_id'
        );

        $nextPertemuanOptions = $this->getNextPertemuanOptions(
            $mengajars
        );

        return view(
            'guru.pertemuan.create',
            compact(
                'mengajars',
                'selectedMengajarId',
                'nextPertemuanOptions'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
            ],
            'pertemuan_ke' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . self::MAX_PERTEMUAN,
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'jam_mulai' => [
                'nullable',
                'date_format:H:i',
            ],
            'jam_selesai' => [
                'nullable',
                'date_format:H:i',
                'after:jam_mulai',
            ],
            'materi' => [
                'nullable',
                'string',
            ],
        ], [
            'pertemuan_ke.max' => 'Nomor pertemuan tidak boleh melebihi ' . self::MAX_PERTEMUAN . ' pertemuan per semester.',
        ]);

        $mengajar = $this->getMengajarMilikGuru(
            $validated['mengajar_id']
        );

        $validated = $this->completePertemuanData(
            $validated,
            $mengajar
        );

        $this->validatePertemuan(
            $validated,
            $mengajar
        );

        Pertemuan::create($validated);

        return redirect()
            ->route('guru.pertemuan.index')
            ->with(
                'success',
                'Pertemuan pembelajaran berhasil ditambahkan.'
            );
    }

    public function show(Pertemuan $pertemuan): View
    {
        $this->authorizePertemuan($pertemuan);

        $pertemuan->load([
            'mengajar.semester.tahunAkademik',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'absensis.siswa',
        ]);

        return view(
            'guru.pertemuan.show',
            compact('pertemuan')
        );
    }

    public function edit(Pertemuan $pertemuan): View
    {
        $this->authorizePertemuan($pertemuan);

        $guru = Auth::user()->guru;

        $mengajars = Mengajar::with([
            'semester.tahunAkademik',
            'kelasAkademik.kelas.jurusan',
            'mataPelajaran',
            'jadwals.ruangan',
        ])
            ->where('guru_id', $guru->id)
            ->whereHas(
                'semester',
                fn ($query) => $query->where(
                    'is_active',
                    true
                )
            )
            ->get();

        $nextPertemuanOptions = $this->getNextPertemuanOptions(
            $mengajars,
            $pertemuan
        );

        return view(
            'guru.pertemuan.edit',
            compact(
                'pertemuan',
                'mengajars',
                'nextPertemuanOptions'
            )
        );
    }

    public function update(
        Request $request,
        Pertemuan $pertemuan
    ): RedirectResponse {
        $this->authorizePertemuan($pertemuan);

        $validated = $request->validate([
            'mengajar_id' => [
                'required',
                'exists:mengajars,id',
            ],
            'pertemuan_ke' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . self::MAX_PERTEMUAN,
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'jam_mulai' => [
                'nullable',
                'date_format:H:i',
            ],
            'jam_selesai' => [
                'nullable',
                'date_format:H:i',
                'after:jam_mulai',
            ],
            'materi' => [
                'nullable',
                'string',
            ],
        ], [
            'pertemuan_ke.max' => 'Nomor pertemuan tidak boleh melebihi ' . self::MAX_PERTEMUAN . ' pertemuan per semester.',
        ]);

        $mengajar = $this->getMengajarMilikGuru(
            $validated['mengajar_id']
        );

        $validated = $this->completePertemuanData(
            $validated,
            $mengajar,
            $pertemuan
        );

        if (
            $pertemuan->mengajar_id
                !== $mengajar->id
            && $pertemuan->absensis()->exists()
        ) {
            throw ValidationException::withMessages([
                'mengajar_id' => 'Penugasan mengajar tidak dapat diubah karena pertemuan sudah memiliki data absensi.',
            ]);
        }

        $this->validatePertemuan(
            $validated,
            $mengajar,
            $pertemuan->id
        );

        $pertemuan->update($validated);

        return redirect()
            ->route('guru.pertemuan.index')
            ->with(
                'success',
                'Pertemuan pembelajaran berhasil diperbarui.'
            );
    }

    public function destroy(
        Pertemuan $pertemuan
    ): RedirectResponse {
        $this->authorizePertemuan($pertemuan);

        if ($pertemuan->absensis()->exists()) {
            return back()->with(
                'error',
                'Pertemuan tidak dapat dihapus karena sudah memiliki data absensi.'
            );
        }

        $pertemuan->delete();

        return redirect()
            ->route('guru.pertemuan.index')
            ->with(
                'success',
                'Pertemuan pembelajaran berhasil dihapus.'
            );
    }

    private function getMengajarMilikGuru(
        int $mengajarId
    ): Mengajar {
        return Mengajar::with([
            'semester',
            'jadwals',
        ])
            ->whereKey($mengajarId)
            ->where(
                'guru_id',
                Auth::user()->guru?->id
            )
            ->firstOrFail();
    }

    private function authorizePertemuan(
        Pertemuan $pertemuan
    ): void {
        abort_unless(
            $pertemuan->mengajar?->guru_id
                === Auth::user()->guru?->id,
            403,
            'Anda tidak memiliki akses ke pertemuan ini.'
        );
    }

    private function validatePertemuan(
        array $validated,
        Mengajar $mengajar,
        ?int $exceptPertemuanId = null
    ): void {
        if (! $mengajar->semester->is_active) {
            throw ValidationException::withMessages([
                'mengajar_id' => 'Pertemuan hanya dapat dibuat pada semester aktif.',
            ]);
        }

        $tanggal = $validated['tanggal'];

        if (
            $tanggal
                < $mengajar->semester
                    ->tanggal_mulai
                    ->format('Y-m-d')
            || $tanggal
                > $mengajar->semester
                    ->tanggal_selesai
                    ->format('Y-m-d')
        ) {
            throw ValidationException::withMessages([
                'tanggal' => 'Tanggal pertemuan harus berada dalam periode semester.',
            ]);
        }

        $query = Pertemuan::where(
            'mengajar_id',
            $mengajar->id
        )
            ->where(
                'pertemuan_ke',
                $validated['pertemuan_ke']
            );

        if ($exceptPertemuanId) {
            $query->whereKeyNot($exceptPertemuanId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'pertemuan_ke' => 'Nomor pertemuan sudah digunakan pada mata pelajaran dan kelas ini.',
            ]);
        }
    }

    private function getNextPertemuanOptions(
        $mengajars,
        ?Pertemuan $pertemuan = null
    ): array {
        return $mengajars
            ->mapWithKeys(function (Mengajar $mengajar) use (
                $pertemuan
            ) {
                $lastPertemuanKe = Pertemuan::where(
                    'mengajar_id',
                    $mengajar->id
                )
                    ->when(
                        $pertemuan,
                        fn ($query) => $query->whereKeyNot(
                            $pertemuan->id
                        )
                    )
                    ->max('pertemuan_ke') ?? 0;

                return [
                    $mengajar->id => $lastPertemuanKe + 1,
                ];
            })
            ->all();
    }

    private function completePertemuanData(
        array $validated,
        Mengajar $mengajar,
        ?Pertemuan $pertemuan = null
    ): array {
        if (empty($validated['pertemuan_ke'])) {
            $next = Pertemuan::where(
                'mengajar_id',
                $mengajar->id
            )
                ->when(
                    $pertemuan,
                    fn ($query) => $query->whereKeyNot(
                        $pertemuan->id
                    )
                )
                ->max('pertemuan_ke') + 1;

            if ($next > self::MAX_PERTEMUAN) {
                throw ValidationException::withMessages([
                    'pertemuan_ke' => 'Jumlah pertemuan untuk kelas dan mata pelajaran ini telah mencapai batas maksimal (' . self::MAX_PERTEMUAN . ' pertemuan per semester).',
                ]);
            }

            $validated['pertemuan_ke'] = $next;
        }

        if (
            empty($validated['jam_mulai'])
            || empty($validated['jam_selesai'])
        ) {
            $jadwal = $this->getJadwalByTanggal(
                $mengajar,
                $validated['tanggal']
            );

            $validated['jam_mulai'] = $validated['jam_mulai']
                ?: ($jadwal?->jam_mulai
                    ? substr($jadwal->jam_mulai, 0, 5)
                    : null);

            $validated['jam_selesai'] = $validated['jam_selesai']
                ?: ($jadwal?->jam_selesai
                    ? substr($jadwal->jam_selesai, 0, 5)
                    : null);
        }

        return $validated;
    }

    private function getJadwalByTanggal(
        Mengajar $mengajar,
        string $tanggal
    ) {
        $hari = $this->hariIndonesia($tanggal);

        return $mengajar
            ->jadwals
            ->firstWhere('hari', $hari)
            ?? $mengajar->jadwals->first();
    }

    private function hariIndonesia(string $tanggal): string
    {
        return [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu',
        ][strtolower(Carbon::parse($tanggal)->format('l'))];
    }
}
