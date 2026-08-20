@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Halo, {{ auth()->user()->name ?? 'Guru' }}</h5>
                            <p class="mb-4">
                                Selamat datang di dashboard Guru. Pantau jadwal, absensi, dan nilai dari area ini.
                            </p>
                            @if ($kelasWali)
                                <div class="alert alert-primary mb-0" role="alert">
                                    Anda juga wali kelas {{ $kelasWali->nama_lengkap }} tahun akademik {{ $kelasWali->tahunAkademik?->nama ?? '-' }}.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img
                                src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140"
                                alt="Dashboard Guru"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 order-1">
            <div class="card">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-book-reader"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Role Aktif</span>
                    <h3 class="card-title mb-2">{{ $kelasWali ? 'Guru & Wali Kelas' : 'Guru' }}</h3>
                    <small class="text-muted">{{ $kelasWali ? $kelasWali->nama_lengkap : 'Akses pembelajaran' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Jadwal Mengajar Hari Ini</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Kelas</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jadwalHariIni as $jadwal)
                        <tr>
                            <td><strong>{{ $jadwal->jam }}</strong></td>
                            <td>{{ $jadwal->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Tidak ada jadwal mengajar hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($kelasWali)
        <div class="row">
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="avatar flex-shrink-0 mb-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-group"></i></span>
                        </div>
                        <span class="fw-semibold d-block mb-1">Siswa Wali</span>
                        <h3 class="card-title mb-2">{{ $waliJumlahSiswa }}</h3>
                        <small class="text-muted">{{ $kelasWali->nama_lengkap }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="avatar flex-shrink-0 mb-3">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-book"></i></span>
                        </div>
                        <span class="fw-semibold d-block mb-1">Mata Pelajaran</span>
                        <h3 class="card-title mb-2">{{ $waliJumlahMapel }}</h3>
                        <small class="text-muted">Di kelas wali</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="avatar flex-shrink-0 mb-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-calendar-check"></i></span>
                        </div>
                        <span class="fw-semibold d-block mb-1">Kehadiran</span>
                        <h3 class="card-title mb-2">{{ $waliPersentaseKehadiran }}%</h3>
                        <small class="text-muted">Rekap kelas wali</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="avatar flex-shrink-0 mb-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-bar-chart-alt-2"></i></span>
                        </div>
                        <span class="fw-semibold d-block mb-1">Rata-rata Nilai</span>
                        <h3 class="card-title mb-2">{{ $waliRataRataNilai }}</h3>
                        <small class="text-muted">Rekap kelas wali</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="mb-0">Akses Wali Kelas</h5>
                <span class="badge bg-label-primary">{{ $kelasWali->tahunAkademik?->nama ?? '-' }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('wali-kelas.siswa.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bx bx-group me-1"></i>
                            Data Siswa Wali
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('wali-kelas.absensi.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bx bx-calendar-check me-1"></i>
                            Rekap Absensi
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('wali-kelas.nilai.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bx bx-bar-chart-alt-2 me-1"></i>
                            Rekap Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
