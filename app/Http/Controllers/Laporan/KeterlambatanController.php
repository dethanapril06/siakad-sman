<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\KelasAkademik;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeterlambatanController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAkademikId = $request->integer(
            'tahun_akademik_id'
        );

        if (! $tahunAkademikId) {
            $tahunAkademikId = TahunAkademik::aktif()
                ->value('id');
        }

        $semesterId = $request->integer(
            'semester_id'
        );

        if (! $semesterId) {
            $semesterId = Semester::aktif()
                ->when(
                    $tahunAkademikId,
                    fn ($query) => $query->where(
                        'tahun_akademik_id',
                        $tahunAkademikId
                    )
                )
                ->value('id');
        }

        $kelasAkademikId = $request->integer(
            'kelas_akademik_id'
        );

        $mataPelajaranId = $request->integer(
            'mata_pelajaran_id'
        );

        $guruId = $request->integer(
            'guru_id'
        );

        $tanggalMulai = $request->date(
            'tanggal_mulai'
        );

        $tanggalSelesai = $request->date(
            'tanggal_selesai'
        );

        /*
        |--------------------------------------------------------------------------
        | Data Keterlambatan
        |--------------------------------------------------------------------------
        */

        $keterlambatans = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.semester.tahunAkademik',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->where(
                'status',
                'terlambat'
            )
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
        | Rekap Siswa Terlambat
        |--------------------------------------------------------------------------
        */

        $rekapSiswa = $keterlambatans
            ->groupBy('siswa_id')
            ->map(function ($items) {
                return [
                    'siswa' =>
                        $items->first()->siswa,

                    'jumlah_terlambat' =>
                        $items->count(),

                    'terakhir_terlambat' =>
                        $items
                            ->sortByDesc(
                                fn (Absensi $absensi) =>
                                    $absensi
                                        ->pertemuan
                                        ->tanggal
                                        ->timestamp
                            )
                            ->first()
                            ->pertemuan
                            ->tanggal,

                    'keterlambatans' =>
                        $items->values(),
                ];
            })
            ->sortByDesc('jumlah_terlambat')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $ringkasan = [
            'total_keterlambatan' =>
                $keterlambatans->count(),

            'jumlah_siswa_terlambat' =>
                $keterlambatans
                    ->pluck('siswa_id')
                    ->unique()
                    ->count(),

            'siswa_paling_sering_terlambat' =>
                $rekapSiswa->first(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Filter
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
        $currentItems = $keterlambatans->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $detailKeterlambatans = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $keterlambatans->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'laporan.keterlambatan.index',
            compact(
                'keterlambatans',
                'detailKeterlambatans',
                'rekapSiswa',
                'ringkasan',
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
        $tanggalMulai = $request->date('tanggal_mulai');
        $tanggalSelesai = $request->date('tanggal_selesai');

        $keterlambatans = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
            'pertemuan.mengajar.semester.tahunAkademik',
        ])
            ->where('status', 'terlambat')
            ->when($tahunAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar.kelasAkademik', fn ($sq) => $sq->where('tahun_akademik_id', $tahunAkademikId)))
            ->when($kelasAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar', fn ($sq) => $sq->where('kelas_akademik_id', $kelasAkademikId)))
            ->whereHas('pertemuan.mengajar', function ($q) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($tanggalMulai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '>=', $tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '<=', $tanggalSelesai)))
            ->get()
            ->sortByDesc(fn ($a) => $a->pertemuan?->tanggal?->timestamp ?? 0)
            ->values();

        $rekapSiswa = $keterlambatans->groupBy('siswa_id')->map(function ($items) {
            $first = $items->first();
            return [
                'siswa' => $first->siswa,
                'kelas' => $first->pertemuan?->mengajar?->kelasAkademik?->nama_lengkap ?? '-',
                'jumlah_terlambat' => $items->count(),
            ];
        })->sortByDesc('jumlah_terlambat')->values();

        $semester = Semester::with('tahunAkademik')->find($semesterId);
        $kepalaSekolah = Guru::whereHas('user.role', fn ($q) => $q->where('name', 'kepala_sekolah'))->where('status', 'aktif')->first() ?? Guru::where('status', 'aktif')->first();

        return view('laporan.keterlambatan.cetak', compact('keterlambatans', 'rekapSiswa', 'semester', 'kepalaSekolah'));
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $kelasAkademikId = $request->integer('kelas_akademik_id');
        $mataPelajaranId = $request->integer('mata_pelajaran_id');
        $guruId = $request->integer('guru_id');
        $tanggalMulai = $request->date('tanggal_mulai');
        $tanggalSelesai = $request->date('tanggal_selesai');

        $keterlambatans = Absensi::with([
            'siswa',
            'pertemuan.mengajar.guru',
            'pertemuan.mengajar.mataPelajaran',
            'pertemuan.mengajar.kelasAkademik.kelas.jurusan',
        ])
            ->where('status', 'terlambat')
            ->when($tahunAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar.kelasAkademik', fn ($sq) => $sq->where('tahun_akademik_id', $tahunAkademikId)))
            ->when($kelasAkademikId, fn ($q) => $q->whereHas('pertemuan.mengajar', fn ($sq) => $sq->where('kelas_akademik_id', $kelasAkademikId)))
            ->whereHas('pertemuan.mengajar', function ($q) use ($semesterId, $mataPelajaranId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($mataPelajaranId) $q->where('mata_pelajaran_id', $mataPelajaranId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($tanggalMulai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '>=', $tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->whereHas('pertemuan', fn ($sq) => $sq->whereDate('tanggal', '<=', $tanggalSelesai)))
            ->get()
            ->sortByDesc(fn ($a) => $a->pertemuan?->tanggal?->timestamp ?? 0)
            ->values();

        $filename = 'Laporan_Keterlambatan_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($keterlambatans) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['No', 'Tanggal', 'NISN', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Guru Pengampu', 'Catatan / Alasan']);

            foreach ($keterlambatans as $idx => $item) {
                fputcsv($handle, [
                    $idx + 1,
                    $item->pertemuan?->tanggal?->format('d/m/Y') ?? '-',
                    $item->siswa?->nisn ?? '-',
                    $item->siswa?->nis ?? '-',
                    $item->siswa?->nama ?? '-',
                    $item->pertemuan?->mengajar?->kelasAkademik?->nama_lengkap ?? '-',
                    $item->pertemuan?->mengajar?->mataPelajaran?->nama ?? '-',
                    $item->pertemuan?->mengajar?->guru?->nama ?? '-',
                    $item->catatan ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}