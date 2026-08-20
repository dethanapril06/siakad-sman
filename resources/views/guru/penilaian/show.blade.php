@extends('layouts.guru')

@section('title', 'Detail Penilaian')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran / Penilaian /</span> Detail</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.nilai.edit', $penilaian) }}" class="btn btn-primary">
                <i class="bx bx-list-check me-1"></i>
                Input Nilai
            </a>
            <a href="{{ route('guru.penilaian.edit', $penilaian) }}" class="btn btn-outline-primary">
                <i class="bx bx-edit-alt me-1"></i>
                Edit
            </a>
            <a href="{{ route('guru.penilaian.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <h5 class="card-header">Informasi Penilaian</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Judul</small>
                    <span class="fw-semibold">{{ $penilaian->judul }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jenis Nilai</small>
                    <span class="fw-semibold">{{ $penilaian->jenisNilai?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tanggal</small>
                    <span class="fw-semibold">{{ $penilaian->tanggal->format('d M Y') }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Mengajar</small>
                    <span class="fw-semibold">
                        {{ $penilaian->mengajar?->mataPelajaran?->nama ?? '-' }}
                        - {{ $penilaian->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}
                    </span>
                </div>
                <div class="col-md-12">
                    <small class="text-muted d-block">Keterangan</small>
                    <span class="fw-semibold">{{ $penilaian->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Nilai Siswa</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Nilai</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($penilaian->nilais as $nilai)
                        <tr>
                            <td><strong>{{ $nilai->siswa?->nis ?? '-' }}</strong></td>
                            <td>{{ $nilai->siswa?->nama ?? '-' }}</td>
                            <td>{{ number_format((float) $nilai->nilai, 2) }}</td>
                            <td>{{ $nilai->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Nilai siswa belum diisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
