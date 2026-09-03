@extends('layouts.guru')

@section('title', 'Detail Mengajar')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran / Mengajar /</span> Detail</h4>
        <a href="{{ route('guru.mengajar.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Mengajar</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Mata Pelajaran</small>
                    <span class="fw-semibold">{{ $mengajar->mataPelajaran?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ ucfirst($mengajar->semester?->nama ?? '-') }}
                        - {{ $mengajar->semester?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jumlah Siswa</small>
                    <span class="fw-semibold">{{ $mengajar->kelasAkademik?->anggotaKelas?->count() ?? 0 }} siswa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Jadwal</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($mengajar->jadwals as $jadwal)
                                <tr>
                                    <td><strong>{{ ucfirst($jadwal->hari) }}</strong></td>
                                    <td>{{ $jadwal->jam }}</td>
                                    <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Belum ada jadwal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Rekap Aktivitas</h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Pertemuan</small>
                                <h4 class="mb-0">{{ $mengajar->pertemuans->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Penilaian</small>
                                <h4 class="mb-0">{{ $mengajar->penilaians->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Anggota Kelas</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($mengajar->kelasAkademik?->anggotaKelas ?? [] as $anggota)
                        <tr>
                            <td><strong>{{ $anggota->siswa?->nis ?? '-' }}</strong></td>
                            <td>{{ $anggota->siswa?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ ($anggota->siswa?->status ?? '') === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ ucfirst($anggota->siswa?->status ?? '-') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if (($anggota->siswa?->status ?? '') === 'aktif')
                                    <a href="{{ route('guru.nilai.siswa', ['mengajar' => $mengajar->id, 'siswa' => $anggota->siswa_id]) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-edit-alt me-1"></i> Input Nilai
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada anggota kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Penilaian</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis Nilai</th>
                        <th>Nama Penilaian</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($mengajar->penilaians as $penilaian)
                        <tr>
                            <td><strong>{{ $penilaian->jenisNilai?->nama ?? '-' }}</strong></td>
                            <td>{{ $penilaian->judul ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4">Belum ada penilaian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
