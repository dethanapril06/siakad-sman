@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.akademik.jadwal.index' : 'kepala-sekolah.akademik.jadwal.index';
@endphp

@extends($layout)

@section('title', 'Detail Jadwal')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik / Jadwal /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.akademik.jadwal.edit', $jadwal) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Informasi Jadwal</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Hari</small>
                    <span class="fw-semibold">{{ ucfirst($jadwal->hari) }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jam</small>
                    <span class="fw-semibold">{{ $jadwal->jam }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $jadwal->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Mata Pelajaran</small>
                    <span class="fw-semibold">{{ $jadwal->mengajar?->mataPelajaran?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Guru</small>
                    <span class="fw-semibold">{{ $jadwal->mengajar?->guru?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Ruangan</small>
                    <span class="fw-semibold">{{ $jadwal->ruangan?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ ucfirst($jadwal->mengajar?->semester?->nama ?? '-') }}
                        - {{ $jadwal->mengajar?->semester?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
