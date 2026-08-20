@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.master.semester.index' : 'kepala-sekolah.master.semester.index';
@endphp

@extends($layout)

@section('title', 'Detail Semester')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master / Semester /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.master.semester.edit', $semester) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Informasi Semester</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">{{ ucfirst($semester->nama) }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tahun Akademik</small>
                    <span class="fw-semibold">{{ $semester->tahunAkademik?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Status</small>
                    @if ($semester->is_active)
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tanggal Mulai</small>
                    <span class="fw-semibold">{{ $semester->tanggal_mulai->format('d M Y') }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tanggal Selesai</small>
                    <span class="fw-semibold">{{ $semester->tanggal_selesai->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
