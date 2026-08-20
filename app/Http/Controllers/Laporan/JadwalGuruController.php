<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalGuruController extends Controller
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

        $guruId = $request->integer(
            'guru_id'
        );

        $hari = $request
            ->string('hari')
            ->toString();

        /*
        |--------------------------------------------------------------------------
        | Query Jadwal
        |--------------------------------------------------------------------------
        */

        $jadwals = Jadwal::with([
            'mengajar.semester.tahunAkademik',
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereHas(
                'mengajar',
                function ($query) use (
                    $semesterId,
                    $guruId
                ) {
                    if ($semesterId) {
                        $query->where(
                            'semester_id',
                            $semesterId
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
                $hari,
                fn ($query) => $query->where(
                    'hari',
                    $hari
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
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Kelompokkan Berdasarkan Guru
        |--------------------------------------------------------------------------
        */

        $jadwalPerGuru = $jadwals
            ->groupBy(
                fn (Jadwal $jadwal) =>
                    $jadwal->mengajar->guru_id
            )
            ->map(function ($items) {
                $totalMenit = $items
                    ->sum(function (
                        Jadwal $jadwal
                    ) {
                        $mulai = strtotime(
                            $jadwal->jam_mulai
                        );

                        $selesai = strtotime(
                            $jadwal->jam_selesai
                        );

                        return max(
                            0,
                            ($selesai - $mulai) / 60
                        );
                    });

                return [
                    'guru' => $items
                        ->first()
                        ->mengajar
                        ->guru,

                    'jumlah_jadwal' =>
                        $items->count(),

                    'total_jam_per_minggu' =>
                        round(
                            $totalMenit / 60,
                            2
                        ),

                    'jadwals' =>
                        $items->values(),
                ];
            })
            ->sortBy(
                fn ($item) =>
                    $item['guru']->nama
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $ringkasan = [
            'jumlah_guru' => $jadwalPerGuru
                ->count(),

            'jumlah_jadwal' => $jadwals
                ->count(),

            'total_jam_mengajar' =>
                round(
                    $jadwalPerGuru->sum(
                        'total_jam_per_minggu'
                    ),
                    2
                ),
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

        $gurus = Guru::whereHas(
            'mengajars',
            fn ($query) => $semesterId
                ? $query->where(
                    'semester_id',
                    $semesterId
                )
                : $query
        )
            ->orderBy('nama')
            ->get();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $jadwalPerGuru->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedJadwalPerGuru = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $jadwalPerGuru->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view(
            'laporan.jadwal-guru.index',
            compact(
                'jadwalPerGuru',
                'paginatedJadwalPerGuru',
                'ringkasan',
                'tahunAkademiks',
                'semesters',
                'gurus',
                'tahunAkademikId',
                'semesterId',
                'guruId',
                'hari'
            )
        );
    }

    public function cetak(Request $request): View
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $guruId = $request->integer('guru_id');
        $hari = $request->string('hari')->toString();

        $jadwals = Jadwal::with([
            'mengajar.semester.tahunAkademik',
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereHas('mengajar', function ($q) use ($semesterId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($hari, fn ($q) => $q->where('hari', $hari))
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
            ->get();

        $jadwalPerGuru = $jadwals->groupBy(fn ($j) => $j->mengajar->guru_id)->map(function ($items) {
            $totalMenit = $items->sum(function ($j) {
                return max(0, (strtotime($j->jam_selesai) - strtotime($j->jam_mulai)) / 60);
            });

            return [
                'guru' => $items->first()->mengajar->guru,
                'jumlah_jadwal' => $items->count(),
                'total_jam_per_minggu' => round($totalMenit / 60, 2),
                'jadwals' => $items->values(),
            ];
        })->sortBy(fn ($item) => $item['guru']->nama)->values();

        $semester = Semester::with('tahunAkademik')->find($semesterId);
        $kepalaSekolah = Guru::whereHas('user.role', fn ($q) => $q->where('name', 'kepala_sekolah'))->where('status', 'aktif')->first() ?? Guru::where('status', 'aktif')->first();

        return view('laporan.jadwal-guru.cetak', compact('jadwalPerGuru', 'semester', 'kepalaSekolah'));
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tahunAkademikId = $request->integer('tahun_akademik_id') ?: TahunAkademik::aktif()->value('id');
        $semesterId = $request->integer('semester_id') ?: Semester::aktif()->when($tahunAkademikId, fn ($q) => $q->where('tahun_akademik_id', $tahunAkademikId))->value('id');
        $guruId = $request->integer('guru_id');
        $hari = $request->string('hari')->toString();

        $jadwals = Jadwal::with([
            'mengajar.guru',
            'mengajar.kelasAkademik.kelas.jurusan',
            'mengajar.mataPelajaran',
            'ruangan',
        ])
            ->whereHas('mengajar', function ($q) use ($semesterId, $guruId) {
                if ($semesterId) $q->where('semester_id', $semesterId);
                if ($guruId) $q->where('guru_id', $guruId);
            })
            ->when($hari, fn ($q) => $q->where('hari', $hari))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $filename = 'Laporan_Jadwal_Guru_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($jadwals) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['No', 'NIP Guru', 'Nama Guru', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Mata Pelajaran', 'Kelas', 'Ruangan']);

            foreach ($jadwals as $idx => $item) {
                fputcsv($handle, [
                    $idx + 1,
                    $item->mengajar?->guru?->nip ?? '-',
                    $item->mengajar?->guru?->nama ?? '-',
                    ucfirst($item->hari),
                    $item->jam_mulai,
                    $item->jam_selesai,
                    $item->mengajar?->mataPelajaran?->nama ?? '-',
                    $item->mengajar?->kelasAkademik?->nama_lengkap ?? '-',
                    $item->ruangan?->nama ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}