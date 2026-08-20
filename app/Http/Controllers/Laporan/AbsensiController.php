<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun_akademik_id' => [
                'nullable',
                'exists:tahun_akademiks,id',
            ],
            'semester_id' => [
                'nullable',
                'exists:semesters,id',
            ],
            'kelas_akademik_id' => [
                'nullable',
                'exists:kelas_akademiks,id',
            ],
            'mata_pelajaran_id' => [
                'nullable',
                'exists:mata_pelajarans,id',
            ],
            'guru_id' => [
                'nullable',
                'exists:gurus,id',
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'hadir',
                    'sakit',
                    'izin',
                    'alpa',
                    'terlambat',
                ]),
            ],
            'tanggal_mulai' => [
                'nullable',
                'date',
            ],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        $tahunAkademikId = isset(
            $validated['tahun_akademik_id']
        )
            ? (int) $validated['tahun_akademik_id']
            : TahunAkademik::aktif()->value('id');

        $semesterId = isset(
            $validated['semester_id']
        )
            ? (int) $validated['semester_id']
            : Semester::aktif()
                ->when(
                    $tahunAkademikId,
                    fn ($query) => $query->where(
                        'tahun_akademik_id',
                        $tahunAkademikId
                    )
                )
                ->value('id');

        $kelasAkademikId = isset(
            $validated['kelas_akademik_id']
        )
            ? (int) $validated['kelas_akademik_id']
            : null;

        $mataPelajaranId = isset(
            $validated['mata_pelajaran_id']
        )
            ? (int) $validated['mata_pelajaran_id']
            : null;

        $guruId = isset(
            $validated['guru_id']
        )
            ? (int) $validated['guru_id']
            : null;

        $status = $validated['status'] ?? null;

        $tanggalMulai = $validated[
            'tanggal_mulai'
        ] ?? null;

        $tanggalSelesai = $validated[
            'tanggal_selesai'
        ] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Query Absensi
        |--------------------------------------------------------------------------
        */

        $absensis = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.semester.tahunAkademik',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->whereHas(
                'pertemuan.mengajar',
                function ($query) use (
                    $semesterId,
                    $kelasAkademikId,
                    $mataPelajaranId,
                    $guruId
                ) {
                    if ($semesterId) {
                        $query->where(
                            'semester_id',
                            $semesterId
                        );
                    }

                    if ($kelasAkademikId) {
                        $query->where(
                            'kelas_akademik_id',
                            $kelasAkademikId
                        );
                    }

                    if ($mataPelajaranId) {
                        $query->where(
                            'mata_pelajaran_id',
                            $mataPelajaranId
                        );
                    }

                    if ($guruId) {
                        $query->where(
                            'guru_id',
                            $guruId
                        );
                    }
                }
            )
            ->when(
                $status,
                fn ($query) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $tanggalMulai,
                fn ($query) => $query->whereHas(
                    'pertemuan',
                    fn ($query) => $query->whereDate(
                        'tanggal',
                        '>=',
                        $tanggalMulai
                    )
                )
            )
            ->when(
                $tanggalSelesai,
                fn ($query) => $query->whereHas(
                    'pertemuan',
                    fn ($query) => $query->whereDate(
                        'tanggal',
                        '<=',
                        $tanggalSelesai
                    )
                )
            )
            ->get()
            ->sortByDesc(
                fn (Absensi $absensi) =>
                    $absensi
                        ->pertemuan
                        ->tanggal
                        ->timestamp
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Rekap Umum
        |--------------------------------------------------------------------------
        */

        $rekapUmum = $this->hitungRekap(
            $absensis
        );

        /*
        |--------------------------------------------------------------------------
        | Rekap Per Siswa
        |--------------------------------------------------------------------------
        */

        $rekapPerSiswa = $absensis
            ->groupBy('siswa_id')
            ->map(function ($items) {
                $rekap = $this->hitungRekap(
                    $items
                );

                return [
                    'siswa' => $items
                        ->first()
                        ->siswa,

                    ...$rekap,
                ];
            })
            ->sortBy(
                fn ($item) =>
                    $item['siswa']->nama
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Rekap Per Kelas
        |--------------------------------------------------------------------------
        */

        $rekapPerKelas = $absensis
            ->groupBy(
                fn (Absensi $absensi) =>
                    $absensi
                        ->pertemuan
                        ->mengajar
                        ->kelas_akademik_id
            )
            ->map(function ($items) {
                $rekap = $this->hitungRekap(
                    $items
                );

                return [
                    'kelas_akademik' => $items
                        ->first()
                        ->pertemuan
                        ->mengajar
                        ->kelasAkademik,

                    ...$rekap,
                ];
            })
            ->sortBy(
                fn ($item) =>
                    $item['kelas_akademik']
                        ->nama_lengkap
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Rekap Per Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        $rekapPerMataPelajaran = $absensis
            ->groupBy(
                fn (Absensi $absensi) =>
                    $absensi
                        ->pertemuan
                        ->mengajar
                        ->mata_pelajaran_id
            )
            ->map(function ($items) {
                $rekap = $this->hitungRekap(
                    $items
                );

                return [
                    'mata_pelajaran' => $items
                        ->first()
                        ->pertemuan
                        ->mengajar
                        ->mataPelajaran,

                    ...$rekap,
                ];
            })
            ->sortBy(
                fn ($item) =>
                    $item['mata_pelajaran']->nama
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Data Filter
        |--------------------------------------------------------------------------
        */

        $tahunAkademiks = TahunAkademik::orderByDesc(
            'tanggal_mulai'
        )->get();

        $semesters = Semester::with(
            'tahunAkademik'
        )
            ->when(
                $tahunAkademikId,
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                )
            )
            ->orderBy('tanggal_mulai')
            ->get();

        $kelasAkademiks = KelasAkademik::with([
            'kelas.jurusan',
            'tahunAkademik',
        ])
            ->when(
                $tahunAkademikId,
                fn ($query) => $query->where(
                    'tahun_akademik_id',
                    $tahunAkademikId
                )
            )
            ->get();

        $mataPelajarans = MataPelajaran::whereHas(
            'mengajars',
            function ($query) use (
                $semesterId,
                $kelasAkademikId
            ) {
                if ($semesterId) {
                    $query->where(
                        'semester_id',
                        $semesterId
                    );
                }

                if ($kelasAkademikId) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasAkademikId
                    );
                }
            }
        )
            ->orderBy('nama')
            ->get();

        $gurus = Guru::whereHas(
            'mengajars',
            function ($query) use (
                $semesterId,
                $kelasAkademikId,
                $mataPelajaranId
            ) {
                if ($semesterId) {
                    $query->where(
                        'semester_id',
                        $semesterId
                    );
                }

                if ($kelasAkademikId) {
                    $query->where(
                        'kelas_akademik_id',
                        $kelasAkademikId
                    );
                }

                if ($mataPelajaranId) {
                    $query->where(
                        'mata_pelajaran_id',
                        $mataPelajaranId
                    );
                }
            }
        )
            ->orderBy('nama')
            ->get();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $absensis->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $detailAbsensis = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $absensis->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'laporan.absensi.index',
            compact(
                'absensis',
                'detailAbsensis',
                'rekapUmum',
                'rekapPerSiswa',
                'rekapPerKelas',
                'rekapPerMataPelajaran',
                'tahunAkademiks',
                'semesters',
                'kelasAkademiks',
                'mataPelajarans',
                'gurus',
                'tahunAkademikId',
                'semesterId',
                'kelasAkademikId',
                'mataPelajaranId',
                'guruId',
                'status',
                'tanggalMulai',
                'tanggalSelesai'
            )
        );
    }

    public function cetak(Request $request): View
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $kelasAkademikId = $request->integer('kelas_akademik_id');
        $mataPelajaranId = $request->integer('mata_pelajaran_id');
        $guruId = $request->integer('guru_id');
        $status = $request->string('status')->toString() ?: null;
        $tanggalMulai = $request->date('tanggal_mulai');
        $tanggalSelesai = $request->date('tanggal_selesai');

        $absensis = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
            'pertemuan.mengajar.semester.tahunAkademik',
        ])
            ->when($tahunAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar.kelasAkademik', fn ($sq) => $sq->where('tahun_akademik_id', $tahunAkademikId)))
            ->when($kelasAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar', fn ($sq) => $sq->where('kelas_akademik_id', $kelasAkademikId)))
            ->whereHas('pertemuan.mengajar', function ($q) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($tanggalMulai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '>=', $tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '<=', $tanggalSelesai)))
            ->get()
            ->sortByDesc(fn ($a) => $a->pertemuan?->tanggal?->timestamp ?? 0)
            ->values();

        $rekap = $this->hitungRekap($absensis);
        $semester = Semester::with('tahunAkademik')->find($semesterId);
        $kepalaSekolah = Guru::whereHas('user.role', fn ($q) => $q->where('name', 'kepala_sekolah'))->where('status', 'aktif')->first() ?? Guru::where('status', 'aktif')->first();

        return view('laporan.absensi.cetak', compact('absensis', 'rekap', 'semester', 'kepalaSekolah'));
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $kelasAkademikId = $request->integer('kelas_akademik_id');
        $mataPelajaranId = $request->integer('mata_pelajaran_id');
        $guruId = $request->integer('guru_id');
        $status = $request->string('status')->toString() ?: null;
        $tanggalMulai = $request->date('tanggal_mulai');
        $tanggalSelesai = $request->date('tanggal_selesai');

        $absensis = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->when($tahunAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar.kelasAkademik', fn ($sq) => $sq->where('tahun_akademik_id', $tahunAkademikId)))
            ->when($kelasAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar', fn ($sq) => $sq->where('kelas_akademik_id', $kelasAkademikId)))
            ->whereHas('pertemuan.mengajar', function ($q) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($tanggalMulai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '>=', $tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '<=', $tanggalSelesai)))
            ->get()
            ->sortByDesc(fn ($a) => $a->pertemuan?->tanggal?->timestamp ?? 0)
            ->values();

        $filename = 'Laporan_Absensi_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($absensis) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['No', 'Tanggal', 'Pertemuan Ke', 'NISN', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Guru Pengampu', 'Status Kehadiran', 'Keterangan']);

            foreach ($absensis as $idx => $item) {
                fputcsv($handle, [
                    $idx + 1,
                    $item->pertemuan?->tanggal?->format('d/m/Y') ?? '-',
                    $item->pertemuan?->pertemuan_ke ?? '-',
                    $item->siswa?->nisn ?? '-',
                    $item->siswa?->nama ?? '-',
                    $item->pertemuan?->mengajar?->kelasAkademik?->nama_lengkap ?? '-',
                    $item->pertemuan?->mengajar?->mataPelajaran?->nama ?? '-',
                    $item->pertemuan?->mengajar?->guru?->nama ?? '-',
                    strtoupper($item->status),
                    $item->catatan ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function hitungRekap(
        $absensis
    ): array {
        $total = $absensis->count();

        $hadir = $absensis
            ->where('status', 'hadir')
            ->count();

        $sakit = $absensis
            ->where('status', 'sakit')
            ->count();

        $izin = $absensis
            ->where('status', 'izin')
            ->count();

        $alpa = $absensis
            ->where('status', 'alpa')
            ->count();

        $terlambat = $absensis
            ->where('status', 'terlambat')
            ->count();

        $jumlahKehadiran = $hadir
            + $terlambat;

        $persentaseKehadiran = $total > 0
            ? round(
                (
                    $jumlahKehadiran
                    / $total
                ) * 100,
                2
            )
            : 0;

        return [
            'total' => $total,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'terlambat' => $terlambat,
            'persentase_kehadiran' =>
                $persentaseKehadiran,
        ];
    }
}