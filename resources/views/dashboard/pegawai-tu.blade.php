@extends('layouts.pegawai-tu')

@section('title', 'Dashboard Pegawai TU')

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Halo, {{ auth()->user()->name ?? 'Pegawai TU' }}</h5>
                            <p class="mb-4">
                                Pantau kesiapan data akademik, absensi, dan nilai dari dashboard monitoring ini.
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-label-primary">
                                    {{ $tahunAkademikAktif?->nama ?? 'Tahun akademik belum aktif' }}
                                </span>
                                <span class="badge bg-label-info">
                                    {{ $semesterAktif?->nama ? ucfirst($semesterAktif->nama) : 'Semester belum aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img
                                src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140"
                                alt="Dashboard Pegawai TU"
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
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user-check"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Role Aktif</span>
                    <h3 class="card-title mb-2">Pegawai TU</h3>
                    <small class="text-muted">Monitoring administrasi akademik</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-group"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Siswa Aktif</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_siswa_aktif'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-chalkboard"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Guru Aktif</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_guru_aktif'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-building-house"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Kelas Aktif</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_kelas_aktif'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="avatar flex-shrink-0 mb-3">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-book-open"></i></span>
                    </div>
                    <span class="fw-semibold d-block mb-1">Penugasan</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_penugasan_mengajar'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Pertemuan</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_pertemuan'] }}</h3>
                    <small class="text-muted">Semester aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Penilaian</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_penilaian'] }}</h3>
                    <small class="text-muted">Semester aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Absensi</span>
                    <h3 class="mb-0">{{ $ringkasan['total_absensi'] }}</h3>
                    <small class="text-muted">Data kehadiran siswa</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Kehadiran</span>
                    <h3 class="mb-0">{{ $ringkasan['persentase_kehadiran'] }}%</h3>
                    <small class="text-muted">Hadir + terlambat</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Rekap Kehadiran Semester Aktif</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Hadir</small>
                    <h4 class="mb-0">{{ $ringkasan['hadir'] }}</h4>
                </div>
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Sakit</small>
                    <h4 class="mb-0">{{ $ringkasan['sakit'] }}</h4>
                </div>
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Izin</small>
                    <h4 class="mb-0">{{ $ringkasan['izin'] }}</h4>
                </div>
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Alpa</small>
                    <h4 class="mb-0">{{ $ringkasan['alpa'] }}</h4>
                </div>
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Terlambat</small>
                    <h4 class="mb-0">{{ $ringkasan['terlambat'] }}</h4>
                </div>
                <div class="col-md-2 col-6">
                    <small class="text-muted d-block">Total</small>
                    <h4 class="mb-0">{{ $ringkasan['total_absensi'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Progres Absensi Terendah</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mengajar</th>
                                <th>Guru</th>
                                <th>Progres</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($progresAbsensi->take(8) as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['mata_pelajaran']?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $item['kelas_akademik']?->nama_lengkap ?? '-' }}</div>
                                    </td>
                                    <td>{{ $item['guru']?->nama ?? '-' }}</td>
                                    <td>{{ $item['pertemuan_lengkap'] }}/{{ $item['jumlah_pertemuan'] }} ({{ $item['persentase'] }}%)</td>
                                    <td>
                                        <span class="badge {{ $item['status'] === 'lengkap' ? 'bg-label-success' : ($item['status'] === 'proses' ? 'bg-label-warning' : 'bg-label-secondary') }}">
                                            {{ str_replace('_', ' ', ucfirst($item['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Data progres absensi belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Progres Nilai Terendah</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mengajar</th>
                                <th>Guru</th>
                                <th>Progres</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($progresNilai->take(8) as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['mata_pelajaran']?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $item['kelas_akademik']?->nama_lengkap ?? '-' }}</div>
                                    </td>
                                    <td>{{ $item['guru']?->nama ?? '-' }}</td>
                                    <td>{{ $item['penilaian_lengkap'] }}/{{ $item['jumlah_penilaian'] }} ({{ $item['persentase'] }}%)</td>
                                    <td>
                                        <span class="badge {{ $item['status'] === 'lengkap' ? 'bg-label-success' : ($item['status'] === 'proses' ? 'bg-label-warning' : 'bg-label-secondary') }}">
                                            {{ str_replace('_', ' ', ucfirst($item['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Data progres nilai belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Pertemuan Terbaru</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mengajar</th>
                                <th>Guru</th>
                                <th>Absensi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($pertemuanTerbaru as $pertemuan)
                                <tr>
                                    <td>{{ $pertemuan->tanggal?->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $pertemuan->mengajar?->mataPelajaran?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $pertemuan->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</div>
                                    </td>
                                    <td>{{ $pertemuan->mengajar?->guru?->nama ?? '-' }}</td>
                                    <td>{{ $pertemuan->absensis_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Pertemuan terbaru belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Penilaian Terbaru</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Penilaian</th>
                                <th>Guru</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($penilaianTerbaru as $penilaian)
                                <tr>
                                    <td>{{ $penilaian->tanggal?->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $penilaian->judul }}</strong>
                                        <div class="small text-muted">
                                            {{ $penilaian->jenisNilai?->nama ?? '-' }}
                                            - {{ $penilaian->mengajar?->mataPelajaran?->nama ?? '-' }}
                                            - {{ $penilaian->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $penilaian->mengajar?->guru?->nama ?? '-' }}</td>
                                    <td>{{ $penilaian->nilais_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Penilaian terbaru belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
