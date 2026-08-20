@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.master.jurusan.index' : 'kepala-sekolah.master.jurusan.index';
@endphp

@extends($layout)

@section('title', 'Detail Jurusan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master / Jurusan /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.master.jurusan.edit', $jurusan) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Informasi Jurusan</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Kode</small>
                    <span class="fw-semibold">{{ $jurusan->kode }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Nama Jurusan</small>
                    <span class="fw-semibold">{{ $jurusan->nama }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Status</small>
                    @if ($jurusan->is_active)
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
