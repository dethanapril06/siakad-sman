@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.laporan.absensi' : 'kepala-sekolah.laporan.absensi';
@endphp

@extends($layout)

@section('title', 'Laporan Absensi')

@section('content')
    @php
        $statusLabels = [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpa' => 'Alpa',
            'terlambat' => 'Terlambat',
        ];

        $statusBadges = [
            'hadir' => 'bg-label-success',
            'sakit' => 'bg-label-info',
            'izin' => 'bg-label-primary',
            'alpa' => 'bg-label-danger',
            'terlambat' => 'bg-label-warning',
        ];

        $tidakHadir = ($rekapUmum['sakit'] ?? 0)
            + ($rekapUmum['izin'] ?? 0)
            + ($rekapUmum['alpa'] ?? 0);
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Absensi</h4>
        <div class="d-flex gap-2">
            <a href="{{ route($routePrefix . '.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bx bx-printer me-1"></i> Cetak PDF
            </a>
            <a href="{{ route($routePrefix . '.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Laporan Absensi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route($routePrefix . '.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="tahun_akademik_id">Tahun Akademik</label>
                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select">
                            <option value="">Tahun aktif</option>
                            @foreach ($tahunAkademiks as $tahunAkademik)
                                <option value="{{ $tahunAkademik->id }}" @selected((string) $tahunAkademikId === (string) $tahunAkademik->id)>
                                    {{ $tahunAkademik->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="semester_id">Semester</label>
                        <select name="semester_id" id="semester_id" class="form-select">
                            <option value="">Semester aktif</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                                    {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="kelas_akademik_id">Kelas</label>
                        <select name="kelas_akademik_id" id="kelas_akademik_id" class="form-select">
                            <option value="">Semua kelas</option>
                            @foreach ($kelasAkademiks as $kelasAkademik)
                                <option value="{{ $kelasAkademik->id }}" @selected((string) $kelasAkademikId === (string) $kelasAkademik->id)>
                                    {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="mata_pelajaran_id">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select">
                            <option value="">Semua mapel</option>
                            @foreach ($mataPelajarans as $mataPelajaran)
                                <option value="{{ $mataPelajaran->id }}" @selected((string) $mataPelajaranId === (string) $mataPelajaran->id)>
                                    {{ $mataPelajaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="guru_id">Guru</label>
                        <select name="guru_id" id="guru_id" class="form-select">
                            <option value="">Semua guru</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->id }}" @selected((string) $guruId === (string) $guru->id)>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua status</option>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Absensi</span>
                    <h3 class="mb-0">{{ $rekapUmum['total'] }}</h3>
                    <small class="text-muted">Sesuai filter</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Hadir</span>
                    <h3 class="mb-0">{{ $rekapUmum['hadir'] }}</h3>
                    <small class="text-muted">Tanpa terlambat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Tidak Hadir</span>
                    <h3 class="mb-0">{{ $tidakHadir }}</h3>
                    <small class="text-muted">Sakit, izin, alpa</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Kehadiran</span>
                    <h3 class="mb-0">{{ number_format($rekapUmum['persentase_kehadiran'], 2) }}%</h3>
                    <small class="text-muted">Hadir dan terlambat</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Sakit</span>
                    <h4 class="mb-0">{{ $rekapUmum['sakit'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Izin</span>
                    <h4 class="mb-0">{{ $rekapUmum['izin'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Alpa</span>
                    <h4 class="mb-0">{{ $rekapUmum['alpa'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Terlambat</span>
                    <h4 class="mb-0">{{ $rekapUmum['terlambat'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Per Kelas</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Total</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Terlambat</th>
                        <th>Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapPerKelas as $rekap)
                        <tr>
                            <td><strong>{{ $rekap['kelas_akademik']?->nama_lengkap ?? '-' }}</strong></td>
                            <td>{{ $rekap['total'] }}</td>
                            <td>{{ $rekap['hadir'] }}</td>
                            <td>{{ $rekap['sakit'] }}</td>
                            <td>{{ $rekap['izin'] }}</td>
                            <td>{{ $rekap['alpa'] }}</td>
                            <td>{{ $rekap['terlambat'] }}</td>
                            <td>
                                <span class="badge {{ $rekap['persentase_kehadiran'] >= 80 ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ number_format($rekap['persentase_kehadiran'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Rekap kelas belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Per Mata Pelajaran</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Total</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Terlambat</th>
                        <th>Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapPerMataPelajaran as $rekap)
                        <tr>
                            <td><strong>{{ $rekap['mata_pelajaran']?->nama ?? '-' }}</strong></td>
                            <td>{{ $rekap['total'] }}</td>
                            <td>{{ $rekap['hadir'] }}</td>
                            <td>{{ $rekap['sakit'] }}</td>
                            <td>{{ $rekap['izin'] }}</td>
                            <td>{{ $rekap['alpa'] }}</td>
                            <td>{{ $rekap['terlambat'] }}</td>
                            <td>
                                <span class="badge {{ $rekap['persentase_kehadiran'] >= 80 ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ number_format($rekap['persentase_kehadiran'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Rekap mata pelajaran belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Per Siswa</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Total</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Terlambat</th>
                        <th>Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapPerSiswa as $rekap)
                        <tr>
                            <td><strong>{{ $rekap['siswa']?->nis ?? '-' }}</strong></td>
                            <td>{{ $rekap['siswa']?->nama ?? '-' }}</td>
                            <td>{{ $rekap['total'] }}</td>
                            <td>{{ $rekap['hadir'] }}</td>
                            <td>{{ $rekap['sakit'] }}</td>
                            <td>{{ $rekap['izin'] }}</td>
                            <td>{{ $rekap['alpa'] }}</td>
                            <td>{{ $rekap['terlambat'] }}</td>
                            <td>
                                <span class="badge {{ $rekap['persentase_kehadiran'] >= 80 ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ number_format($rekap['persentase_kehadiran'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Rekap siswa belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Detail Absensi</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Pertemuan</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($detailAbsensis as $absensi)
                        @php
                            $mengajar = $absensi->pertemuan?->mengajar;
                            $statusAbsensi = $absensi->status;
                        @endphp
                        <tr>
                            <td>{{ $absensi->pertemuan?->tanggal?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <strong>{{ $absensi->siswa?->nama ?? '-' }}</strong>
                                <div class="small text-muted">{{ $absensi->siswa?->nis ?? '-' }}</div>
                            </td>
                            <td>{{ $mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $mengajar?->guru?->nama ?? '-' }}</td>
                            <td>Pertemuan {{ $absensi->pertemuan?->pertemuan_ke ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $statusBadges[$statusAbsensi] ?? 'bg-label-secondary' }}">
                                    {{ $statusLabels[$statusAbsensi] ?? ucfirst((string) $statusAbsensi) }}
                                </span>
                            </td>
                            <td>{{ $absensi->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Data absensi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$detailAbsensis" align="end" />
        </div>
    </div>
@endsection
