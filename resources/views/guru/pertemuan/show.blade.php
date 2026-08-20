@extends('layouts.guru')

@section('title', 'Detail Pertemuan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran / Pertemuan /</span> Detail</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.absensi.edit', $pertemuan) }}" class="btn btn-primary">
                <i class="bx bx-check-square me-1"></i>
                Absensi
            </a>
            <a href="{{ route('guru.pertemuan.edit', $pertemuan) }}" class="btn btn-outline-primary">
                <i class="bx bx-edit-alt me-1"></i>
                Edit
            </a>
            <a href="{{ route('guru.pertemuan.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Pertemuan</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Pertemuan</small>
                    <span class="fw-semibold">Ke-{{ $pertemuan->pertemuan_ke }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tanggal</small>
                    <span class="fw-semibold">{{ $pertemuan->tanggal->format('d M Y') }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Mengajar</small>
                    <span class="fw-semibold">
                        {{ $pertemuan->mengajar?->mataPelajaran?->nama ?? '-' }}
                        - {{ $pertemuan->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jam</small>
                    <span class="fw-semibold">
                        {{ $pertemuan->jam_mulai ? substr($pertemuan->jam_mulai, 0, 5) : '-' }}
                        -
                        {{ $pertemuan->jam_selesai ? substr($pertemuan->jam_selesai, 0, 5) : '-' }}
                    </span>
                </div>
                <div class="col-md-12">
                    <small class="text-muted d-block">Materi</small>
                    <span class="fw-semibold">{{ $pertemuan->materi ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Absensi</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($pertemuan->absensis as $absensi)
                        <tr>
                            <td><strong>{{ $absensi->siswa?->nis ?? '-' }}</strong></td>
                            <td>{{ $absensi->siswa?->nama ?? '-' }}</td>
                            <td>{{ ucfirst($absensi->status) }}</td>
                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Absensi belum diisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
