@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.laporan.jadwal-guru' : 'kepala-sekolah.laporan.jadwal-guru';
@endphp

@extends($layout)

@section('title', 'Laporan Jadwal Guru')

@section('content')
    @php
        $hariOptions = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Jadwal Guru</h4>
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
            <h5 class="mb-0">Filter Jadwal Guru</h5>
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
                        <label class="form-label" for="hari">Hari</label>
                        <select name="hari" id="hari" class="form-select">
                            <option value="">Semua hari</option>
                            @foreach ($hariOptions as $value => $label)
                                <option value="{{ $value }}" @selected($hari === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
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
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Jumlah Guru</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_guru'] }}</h3>
                    <small class="text-muted">Guru dengan jadwal</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Jumlah Jadwal</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_jadwal'] }}</h3>
                    <small class="text-muted">Sesuai filter</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Jam Mengajar</span>
                    <h3 class="mb-0">{{ number_format($ringkasan['total_jam_mengajar'], 2) }}</h3>
                    <small class="text-muted">Jam per minggu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Jadwal Per Guru</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guru</th>
                        <th>Jumlah Jadwal</th>
                        <th>Total Jam/Minggu</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jadwalPerGuru as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $laporan['guru']?->nama ?? '-' }}</strong>
                                <div class="small text-muted">{{ $laporan['guru']?->nip ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $laporan['jumlah_jadwal'] }} jadwal</span>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ number_format($laporan['total_jam_per_minggu'], 2) }} jam</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Rekap jadwal guru belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @forelse ($paginatedJadwalPerGuru as $laporan)
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">{{ $laporan['guru']?->nama ?? '-' }}</h5>
                    <small class="text-muted">{{ $laporan['guru']?->nip ?? 'NIP belum tersedia' }}</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-label-primary">{{ $laporan['jumlah_jadwal'] }} jadwal</span>
                    <span class="badge bg-label-info">{{ number_format($laporan['total_jam_per_minggu'], 2) }} jam/minggu</span>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Ruangan</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($laporan['jadwals'] as $jadwal)
                            @php($mengajar = $jadwal->mengajar)
                            <tr>
                                <td><strong>{{ $hariOptions[$jadwal->hari] ?? ucfirst((string) $jadwal->hari) }}</strong></td>
                                <td>{{ $jadwal->jam }}</td>
                                <td>{{ $mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                                <td>{{ $mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                                <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                                <td>
                                    {{ ucfirst($mengajar?->semester?->nama ?? '-') }}
                                    <div class="small text-muted">{{ $mengajar?->semester?->tahunAkademik?->nama ?? '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Detail jadwal belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-4">Data jadwal guru belum tersedia.</div>
        </div>
    @endforelse

    <div class="d-flex justify-content-end my-4">
        <x-pagination :paginator="$paginatedJadwalPerGuru" align="end" />
    </div>
@endsection
