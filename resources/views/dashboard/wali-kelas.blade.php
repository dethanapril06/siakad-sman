@extends('layouts.guru')

@section('title', 'Dashboard Wali Kelas - ' . $kelasWali->nama_lengkap)

@section('content')
    {{-- Role & Mode Switcher Bar --}}
    <div class="card mb-4 bg-primary text-white border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-white text-primary p-2 rounded">
                        <i class="bx bx-user-pin fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold">Portal Wali Kelas: {{ $kelasWali->nama_lengkap }} ({{ $kelasWali->kelas?->jurusan?->nama }})</h6>
                        <small class="text-white-50">Tahun Pelajaran {{ $semesterAktif?->tahunAkademik?->nama }} &bull; Semester {{ ucfirst($semesterAktif?->nama ?? '-') }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-light text-white d-none d-md-inline-block">Mode Wali Kelas Aktif</span>
                    <a href="{{ route('guru.dashboard') }}" class="btn btn-sm btn-light text-primary fw-semibold">
                        <i class="bx bx-left-arrow-alt me-1"></i> Kembali ke Dashboard Guru
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 KPI Metric Cards --}}
    <div class="row g-4 mb-4">
        {{-- Total Siswa --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-muted fw-semibold">Siswa Binaan</span>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-group fs-4"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="card-title mb-1 fw-bold text-primary">{{ $totalSiswa }}</h3>
                    <small class="text-muted">Orang dalam 1 Rombel</small>
                </div>
            </div>
        </div>

        {{-- Rata-Rata Nilai --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-muted fw-semibold">Rata-Rata Kelas</span>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-line-chart fs-4"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="card-title mb-1 fw-bold text-info">{{ $rataRataKelas }}</h3>
                    <small class="text-muted">{{ $jumlahMapel }} Mapel Terdaftar (KKM 75)</small>
                </div>
            </div>
        </div>

        {{-- Kehadiran Rombel --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-muted fw-semibold">Tingkat Kehadiran</span>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-calendar-check fs-4"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="card-title mb-1 fw-bold text-success">{{ $persentaseKehadiran }}%</h3>
                    <small class="text-muted">Total {{ $hadirCount + $terlambatCount }} Sesi Hadir</small>
                </div>
            </div>
        </div>

        {{-- Progress E-Rapor --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-muted fw-semibold">Catatan Rapor</span>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded {{ $persenCatatan == 100 ? 'bg-label-success' : 'bg-label-warning' }}">
                                <i class="bx bx-certification fs-4"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="card-title mb-1 fw-bold {{ $persenCatatan == 100 ? 'text-success' : 'text-warning' }}">{{ $totalCatatanDiisi }} / {{ $totalSiswa }}</h3>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar {{ $persenCatatan == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $persenCatatan }}%;" aria-valuenow="{{ $persenCatatan }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted mt-1 d-block">{{ $persenCatatan }}% Catatan Terisi</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Hub --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('wali-kelas.rapor.index') }}" class="card card-hover border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-edit fs-4"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">E-Rapor & Catatan</h6>
                        <small class="text-muted">Kelola nilai & catatan</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('wali-kelas.siswa.index') }}" class="card card-hover border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-user-check fs-4"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">Daftar Siswa</h6>
                        <small class="text-muted">Profil & kontak siswa</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('wali-kelas.absensi.index') }}" class="card card-hover border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-calendar-event fs-4"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">Rekap Presensi</h6>
                        <small class="text-muted">Pantau kehadiran kelas</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('wali-kelas.nilai.index') }}" class="card card-hover border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-bar-chart-alt fs-4"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">Rekap Nilai</h6>
                        <small class="text-muted">Lihat nilai semua mapel</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Main Content: Left (Attendance Stats & Feeds) + Right (Student Monitoring Table) --}}
    <div class="row g-4">
        {{-- Left: Statistik Presensi & Feeds --}}
        <div class="col-lg-4">
            {{-- Card Distribusi Kehadiran --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Statistik Presensi</h5>
                    <span class="badge bg-label-primary">{{ $persentaseKehadiran }}% Hadir</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Hadir Tepat Waktu</span>
                            <span class="fw-bold small text-success">{{ $hadirCount }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalPresensi > 0 ? ($hadirCount / $totalPresensi) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Terlambat</span>
                            <span class="fw-bold small text-warning">{{ $terlambatCount }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalPresensi > 0 ? ($terlambatCount / $totalPresensi) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Sakit</span>
                            <span class="fw-bold small text-info">{{ $sakitCount }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $totalPresensi > 0 ? ($sakitCount / $totalPresensi) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Izin</span>
                            <span class="fw-bold small text-primary">{{ $izinCount }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $totalPresensi > 0 ? ($izinCount / $totalPresensi) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Alpa / Tanpa Keterangan</span>
                            <span class="fw-bold small text-danger">{{ $alpaCount }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalPresensi > 0 ? ($alpaCount / $totalPresensi) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feed Presensi Terkini --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Presensi Terkini</h5>
                    <a href="{{ route('wali-kelas.absensi.index') }}" class="small text-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($recentAbsensi as $abs)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <div class="fw-bold text-dark small">{{ $abs->siswa?->nama }}</div>
                                    <small class="text-muted">{{ $abs->pertemuan?->mengajar?->mataPelajaran?->nama }} &bull; {{ $abs->pertemuan?->tanggal?->format('d/m/Y') }}</small>
                                </div>
                                <span class="badge {{ match($abs->status) {
                                    'hadir' => 'bg-label-success',
                                    'terlambat' => 'bg-label-warning',
                                    'sakit' => 'bg-label-info',
                                    'izin' => 'bg-label-primary',
                                    'alpa' => 'bg-label-danger',
                                    default => 'bg-label-secondary'
                                } }} text-uppercase small">
                                    {{ $abs->status }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted small">Belum ada catatan presensi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Tabel Monitoring Siswa Binaan --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Monitoring Siswa Binaan</h5>
                        <small class="text-muted">Daftar presensi dan kelengkapan catatan e-rapor</small>
                    </div>
                    <a href="{{ route('wali-kelas.rapor.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-certification me-1"></i> Buka E-Rapor
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Siswa</th>
                                <th style="width: 110px;">Kehadiran</th>
                                <th style="width: 70px;">Alpa</th>
                                <th style="width: 140px;">Catatan Rapor</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswaMonitoring as $idx => $item)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item['siswa']->nama }}</div>
                                        <small class="text-muted">NISN: {{ $item['siswa']->nisn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item['persen_hadir'] >= 85 ? 'bg-label-success' : ($item['persen_hadir'] >= 75 ? 'bg-label-warning' : 'bg-label-danger') }}">
                                            {{ $item['persen_hadir'] }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item['alpa'] > 0)
                                            <span class="badge bg-danger">{{ $item['alpa'] }} Hari</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['has_catatan'])
                                            <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Sudah Diisi</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i> Belum Diisi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('wali-kelas.siswa.show', $item['siswa']->id) }}" class="btn btn-sm btn-icon btn-label-secondary" title="Lihat Profil">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('wali-kelas.rapor.cetak', $item['siswa']->id) }}" target="_blank" class="btn btn-sm btn-icon btn-label-primary" title="Cetak Rapor">
                                            <i class="bx bx-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada siswa terdaftar di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
