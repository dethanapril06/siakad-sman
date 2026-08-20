@extends('layouts.kepala-sekolah')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
    {{-- Banner Selamat Datang & Status Periode --}}
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-2">Halo, {{ auth()->user()->name ?? 'Kepala Sekolah' }} 👋</h5>
                            <p class="mb-3 text-muted">
                                Selamat datang di dashboard monitoring eksekutif. Pantau performa akademik, kehadiran siswa, serta progres pengajaran guru secara terpadu.
                            </p>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-label-primary">
                                    <i class="bx bx-calendar me-1"></i>
                                    {{ $tahunAkademikAktif?->nama ?? 'Tahun Akademik Belum Aktif' }}
                                </span>
                                <span class="badge bg-label-info">
                                    <i class="bx bx-time me-1"></i>
                                    {{ $semesterAktif?->nama ? 'Semester ' . ucfirst($semesterAktif->nama) : 'Semester Belum Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img
                                src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140"
                                alt="Dashboard Kepala Sekolah"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 order-1 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar flex-shrink-0 mb-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-crown"></i></span>
                        </div>
                        <span class="fw-semibold d-block mb-1 text-muted">Hak Akses Aktif</span>
                        <h4 class="card-title mb-1 text-dark">Kepala Sekolah</h4>
                        <small class="text-muted">Akses penuh supervisi & pelaporan akademik</small>
                    </div>
                    <div class="pt-3 border-top">
                        <small class="text-muted d-block">
                            <i class="bx bx-calendar-event me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Metrik Utama (KPI Eksekutif) --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-group"></i></span>
                        </div>
                        <span class="badge bg-label-primary">Total</span>
                    </div>
                    <span class="fw-semibold d-block mb-1 text-muted">Siswa Aktif</span>
                    <h3 class="card-title mb-0">{{ number_format($ringkasan['jumlah_siswa_aktif'] ?? 0) }}</h3>
                    <small class="text-muted">Terdaftar di sistem</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-user-check"></i></span>
                        </div>
                        <span class="badge bg-label-info">Pengajar</span>
                    </div>
                    <span class="fw-semibold d-block mb-1 text-muted">Guru Aktif</span>
                    <h3 class="card-title mb-0">{{ number_format($ringkasan['jumlah_guru_aktif'] ?? 0) }}</h3>
                    <small class="text-muted">Guru pengampu aktif</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-building-house"></i></span>
                        </div>
                        <span class="badge bg-label-success">Rombel</span>
                    </div>
                    <span class="fw-semibold d-block mb-1 text-muted">Kelas Aktif</span>
                    <h3 class="card-title mb-0">{{ number_format($ringkasan['jumlah_kelas_aktif'] ?? 0) }}</h3>
                    <small class="text-muted">Tahun akademik aktif</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-award"></i></span>
                        </div>
                        <span class="badge bg-label-warning">Akademik</span>
                    </div>
                    <span class="fw-semibold d-block mb-1 text-muted">Rata-rata Nilai</span>
                    <h3 class="card-title mb-0">{{ $rataRataNilaiSekolah > 0 ? $rataRataNilaiSekolah : '-' }}</h3>
                    <small class="text-muted">Capaian nilai semester aktif</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Pintasan Modul Laporan Eksekutif --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-2 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Pusat Laporan & Supervisi</h5>
                        <small class="text-muted">Akses cepat ke berkas pelaporan dan rekapitulasi sekolah</small>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('kepala-sekolah.laporan.nilai.index') }}" class="text-decoration-none">
                                <div class="card border border-primary border-opacity-25 shadow-none hover-shadow transition">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-certification"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Laporan Nilai</h6>
                                            <small class="text-muted">Rekap nilai siswa</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('kepala-sekolah.laporan.absensi.index') }}" class="text-decoration-none">
                                <div class="card border border-success border-opacity-25 shadow-none hover-shadow transition">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-calendar-check"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Laporan Absensi</h6>
                                            <small class="text-muted">Rekap presensi siswa</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('kepala-sekolah.laporan.keterlambatan.index') }}" class="text-decoration-none">
                                <div class="card border border-danger border-opacity-25 shadow-none hover-shadow transition">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-time-five"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Keterlambatan</h6>
                                            <small class="text-muted">Rekap kedisiplinan</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('kepala-sekolah.laporan.jadwal-guru.index') }}" class="text-decoration-none">
                                <div class="card border border-info border-opacity-25 shadow-none hover-shadow transition">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-spreadsheet"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Jadwal Guru</h6>
                                            <small class="text-muted">Jadwal mengajar guru</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekapitulasi Presensi Semester Aktif --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-0">Rekapitulasi Kehadiran Siswa</h5>
                <small class="text-muted">Data presensi seluruh siswa pada semester berjalan</small>
            </div>
            <span class="badge bg-label-primary px-3 py-2 fs-6">
                Tingkat Kehadiran: <strong>{{ $ringkasan['persentase_kehadiran'] ?? 0 }}%</strong>
            </span>
        </div>
        <div class="card-body">
            <div class="progress mb-4" style="height: 12px;">
                @php
                    $total = $ringkasan['total_absensi'] > 0 ? $ringkasan['total_absensi'] : 1;
                    $pctHadir = round(($ringkasan['hadir'] / $total) * 100, 1);
                    $pctTerlambat = round(($ringkasan['terlambat'] / $total) * 100, 1);
                    $pctSakit = round(($ringkasan['sakit'] / $total) * 100, 1);
                    $pctIzin = round(($ringkasan['izin'] / $total) * 100, 1);
                    $pctAlpa = round(($ringkasan['alpa'] / $total) * 100, 1);
                @endphp
                <div class="progress-bar bg-success" style="width: {{ $pctHadir }}%" role="progressbar" title="Hadir: {{ $pctHadir }}%"></div>
                <div class="progress-bar bg-warning" style="width: {{ $pctTerlambat }}%" role="progressbar" title="Terlambat: {{ $pctTerlambat }}%"></div>
                <div class="progress-bar bg-info" style="width: {{ $pctIzin }}%" role="progressbar" title="Izin: {{ $pctIzin }}%"></div>
                <div class="progress-bar bg-primary" style="width: {{ $pctSakit }}%" role="progressbar" title="Sakit: {{ $pctSakit }}%"></div>
                <div class="progress-bar bg-danger" style="width: {{ $pctAlpa }}%" role="progressbar" title="Alpa: {{ $pctAlpa }}%"></div>
            </div>

            <div class="row g-3 text-center">
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded">
                        <small class="text-success fw-semibold d-block mb-1"><i class="bx bx-check-circle me-1"></i> Hadir</small>
                        <h4 class="mb-0 text-success">{{ number_format($ringkasan['hadir'] ?? 0) }}</h4>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded">
                        <small class="text-warning fw-semibold d-block mb-1"><i class="bx bx-time me-1"></i> Terlambat</small>
                        <h4 class="mb-0 text-warning">{{ number_format($ringkasan['terlambat'] ?? 0) }}</h4>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded">
                        <small class="text-info fw-semibold d-block mb-1"><i class="bx bx-info-circle me-1"></i> Izin</small>
                        <h4 class="mb-0 text-info">{{ number_format($ringkasan['izin'] ?? 0) }}</h4>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded">
                        <small class="text-primary fw-semibold d-block mb-1"><i class="bx bx-plus-medical me-1"></i> Sakit</small>
                        <h4 class="mb-0 text-primary">{{ number_format($ringkasan['sakit'] ?? 0) }}</h4>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded">
                        <small class="text-danger fw-semibold d-block mb-1"><i class="bx bx-x-circle me-1"></i> Alpa</small>
                        <h4 class="mb-0 text-danger">{{ number_format($ringkasan['alpa'] ?? 0) }}</h4>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-dark fw-semibold d-block mb-1"><i class="bx bx-list-ul me-1"></i> Total Record</small>
                        <h4 class="mb-0 text-dark">{{ number_format($ringkasan['total_absensi'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monitoring Progres Pengajaran Guru (Absensi & Nilai) --}}
    <div class="row">
        {{-- Progres Absensi Guru --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Supervisi Pengisian Absensi</h5>
                        <small class="text-muted">Progres kelengkapan presensi guru per mapel</small>
                    </div>
                    <a href="{{ route('kepala-sekolah.laporan.absensi.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran & Kelas</th>
                                <th>Guru Pengampu</th>
                                <th>Kelengkapan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($progresAbsensi->take(6) as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item['mata_pelajaran']?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item['kelas_akademik']?->nama_lengkap ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $item['guru']?->nama ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-100" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar {{ $item['persentase'] >= 100 ? 'bg-success' : ($item['persentase'] > 50 ? 'bg-primary' : 'bg-warning') }}"
                                                     style="width: {{ $item['persentase'] }}%"></div>
                                            </div>
                                            <span class="small fw-semibold">{{ $item['persentase'] }}%</span>
                                        </div>
                                        <small class="text-muted d-block">{{ $item['pertemuan_lengkap'] }}/{{ $item['jumlah_pertemuan'] }} temu</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item['status'] === 'lengkap' ? 'bg-label-success' : ($item['status'] === 'proses' ? 'bg-label-warning' : 'bg-label-secondary') }}">
                                            {{ str_replace('_', ' ', ucfirst($item['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                        Data monitoring absensi belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Progres Nilai Guru --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Supervisi Pengisian Nilai</h5>
                        <small class="text-muted">Progres kelengkapan nilai siswa per mapel</small>
                    </div>
                    <a href="{{ route('kepala-sekolah.laporan.nilai.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran & Kelas</th>
                                <th>Guru Pengampu</th>
                                <th>Kelengkapan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($progresNilai->take(6) as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item['mata_pelajaran']?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item['kelas_akademik']?->nama_lengkap ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $item['guru']?->nama ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-100" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar {{ $item['persentase'] >= 100 ? 'bg-success' : ($item['persentase'] > 50 ? 'bg-primary' : 'bg-warning') }}"
                                                     style="width: {{ $item['persentase'] }}%"></div>
                                            </div>
                                            <span class="small fw-semibold">{{ $item['persentase'] }}%</span>
                                        </div>
                                        <small class="text-muted d-block">{{ $item['penilaian_lengkap'] }}/{{ $item['jumlah_penilaian'] }} tugas</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item['status'] === 'lengkap' ? 'bg-label-success' : ($item['status'] === 'proses' ? 'bg-label-warning' : 'bg-label-secondary') }}">
                                            {{ str_replace('_', ' ', ucfirst($item['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                        Data monitoring nilai belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Pembelajaran & Penilaian Terkini --}}
    <div class="row">
        {{-- KBM / Pertemuan Terbaru --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Pertemuan & KBM Terkini</h5>
                        <small class="text-muted">Aktivitas pembelajaran yang baru dilaksanakan</small>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Mapel & Kelas</th>
                                <th>Guru</th>
                                <th>Presensi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($pertemuanTerbaru->take(5) as $pertemuan)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ $pertemuan->tanggal?->format('d/m/Y') ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $pertemuan->mengajar?->mataPelajaran?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $pertemuan->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="small">{{ $pertemuan->mengajar?->guru?->nama ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-user-check me-1"></i> {{ $pertemuan->absensis_count }} Siswa
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-calendar-x fs-3 d-block mb-1"></i>
                                        Belum ada data aktivitas pertemuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Penilaian Terkini --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Penilaian Terbaru</h5>
                        <small class="text-muted">Entri penilaian & evaluasi terbaru dari guru</small>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Penilaian</th>
                                <th>Guru</th>
                                <th>Terselesaikan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($penilaianTerbaru->take(5) as $penilaian)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ $penilaian->tanggal?->format('d/m/Y') ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $penilaian->judul }}</div>
                                        <small class="text-muted">
                                            <span class="badge bg-label-primary p-0 px-1">{{ $penilaian->jenisNilai?->nama ?? '-' }}</span>
                                            {{ $penilaian->mengajar?->mataPelajaran?->nama ?? '-' }}
                                            ({{ $penilaian->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }})
                                        </small>
                                    </td>
                                    <td>
                                        <div class="small">{{ $penilaian->mengajar?->guru?->nama ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-check-double me-1"></i> {{ $penilaian->nilais_count }} Nilai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-file-blank fs-3 d-block mb-1"></i>
                                        Belum ada data penilaian terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
