@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.akademik.kelas-akademik.index' : 'kepala-sekolah.akademik.kelas-akademik.index';
@endphp

@extends($layout)

@section('title', 'Detail Kelas Akademik')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik / Kelas Akademik /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.akademik.kelas-akademik.edit', $kelasAkademik) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
                <a href="{{ route('pegawai-tu.akademik.anggota-kelas.index', $kelasAkademik) }}" class="btn btn-outline-primary">
                    <i class="bx bx-group me-1"></i>
                    Anggota
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Kelas Akademik</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $kelasAkademik->nama_lengkap }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tahun Akademik</small>
                    <span class="fw-semibold">{{ $kelasAkademik->tahunAkademik?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Wali Kelas</small>
                    <span class="fw-semibold">{{ $kelasAkademik->waliKelas?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jumlah Anggota</small>
                    <span class="fw-semibold">{{ $kelasAkademik->anggotaKelas->count() }} siswa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Anggota Kelas</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($kelasAkademik->anggotaKelas as $anggota)
                        <tr>
                            <td><strong>{{ $anggota->siswa?->nis ?? '-' }}</strong></td>
                            <td>{{ $anggota->siswa?->nama ?? '-' }}</td>
                            <td>{{ ucfirst($anggota->siswa?->status ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Belum ada anggota kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
