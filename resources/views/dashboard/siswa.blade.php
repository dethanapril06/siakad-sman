@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Halo, {{ auth()->user()->name ?? 'Siswa' }}</h5>
                            <p class="mb-4">
                                Selamat datang di dashboard Siswa. Pantau jadwal, nilai, dan absensi dari halaman ini.
                            </p>
                            @if ($kelasAkademik)
                                <div class="alert alert-primary mb-0" role="alert">
                                    Kamu terdaftar di {{ $kelasAkademik->nama_lengkap }} tahun akademik {{ $kelasAkademik->tahunAkademik?->nama ?? '-' }}.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img
                                src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140"
                                alt="Dashboard Siswa"
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
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-id-card"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Role Aktif</span>
                    <h3 class="card-title mb-2">Siswa</h3>
                    <small class="text-muted">{{ $kelasAkademik?->nama_lengkap ?? 'Akses informasi akademik' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-book"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Jumlah Nilai</span>
                    <h3 class="card-title mb-2">{{ $jumlahNilai }}</h3>
                    <small class="text-muted">{{ $semesterAktif?->nama ? ucfirst($semesterAktif->nama) : 'Semester aktif' }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-bar-chart-alt-2"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Rata-rata Nilai</span>
                    <h3 class="card-title mb-2">{{ $rataRataNilai !== null ? number_format((float) $rataRataNilai, 2) : '0.00' }}</h3>
                    <small class="text-muted">Nilai semester aktif</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-calendar-check"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Kehadiran</span>
                    <h3 class="card-title mb-2">{{ $persentaseKehadiran }}%</h3>
                    <small class="text-muted">{{ $totalAbsensi }} data absensi</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time-five"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Terlambat</span>
                    <h3 class="card-title mb-2">{{ $jumlahTerlambat }}</h3>
                    <small class="text-muted">Semester aktif</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Jadwal Hari Ini</h5>
            <a href="{{ route('siswa.jadwal.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-calendar me-1"></i>
                Lihat Semua
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jadwalHariIni as $jadwal)
                        <tr>
                            <td><strong>{{ $jadwal->jam }}</strong></td>
                            <td>{{ $jadwal->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->mengajar?->guru?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Tidak ada jadwal pelajaran hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="mb-0">Nilai Terbaru</h5>
                    <a href="{{ route('siswa.nilai.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-bar-chart-alt-2 me-1"></i>
                        Lihat Nilai
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Jenis</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($nilaiTerbaru as $nilai)
                                <tr>
                                    <td><strong>{{ $nilai->penilaian?->mengajar?->mataPelajaran?->nama ?? '-' }}</strong></td>
                                    <td>{{ $nilai->penilaian?->jenisNilai?->nama ?? '-' }}</td>
                                    <td>{{ number_format((float) $nilai->nilai, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Nilai terbaru belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-5 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Informasi Akademik</h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <small class="text-muted d-block">NIS</small>
                            <span class="fw-semibold">{{ $siswa->nis }}</span>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted d-block">Kelas Aktif</small>
                            <span class="fw-semibold">{{ $kelasAkademik?->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted d-block">Wali Kelas</small>
                            <span class="fw-semibold">{{ $kelasAkademik?->waliKelas?->nama ?? '-' }}</span>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted d-block">Semester Aktif</small>
                            <span class="fw-semibold">
                                {{ $semesterAktif?->nama ? ucfirst($semesterAktif->nama) : '-' }}
                                @if ($semesterAktif?->tahunAkademik)
                                    - {{ $semesterAktif->tahunAkademik->nama }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('siswa.absensi.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-calendar-check me-1"></i>
                            Lihat Absensi
                        </a>
                        <a href="{{ route('siswa.profile') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-user me-1"></i>
                            Profil Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
