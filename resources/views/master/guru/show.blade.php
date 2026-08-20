@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.master.guru.index' : 'kepala-sekolah.master.guru.index';
@endphp

@extends($layout)

@section('title', 'Detail Guru')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master / Guru /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.master.guru.edit', $guru) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Guru</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">NIP</small>
                    <span class="fw-semibold">{{ $guru->nip }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Nama</small>
                    <span class="fw-semibold">{{ $guru->nama }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Email Login</small>
                    <span class="fw-semibold">{{ $guru->user?->email ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Role</small>
                    <span class="fw-semibold">{{ $guru->user?->role?->name ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Jenis Kelamin</small>
                    <span class="fw-semibold">{{ $guru->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Tempat, Tanggal Lahir</small>
                    <span class="fw-semibold">
                        {{ $guru->tempat_lahir ?? '-' }},
                        {{ $guru->tanggal_lahir?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">No. HP</small>
                    <span class="fw-semibold">{{ $guru->no_hp ?? '-' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Status</small>
                    @if ($guru->status === 'aktif')
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-secondary">Nonaktif</span>
                    @endif
                </div>
                <div class="col-md-12">
                    <small class="text-muted d-block">Alamat</small>
                    <span class="fw-semibold">{{ $guru->alamat ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Data Wali Kelas</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tahun Akademik</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($guru->kelasWali as $kelasWali)
                        <tr>
                            <td>{{ $kelasWali->kelas?->nama ?? '-' }}</td>
                            <td>{{ $kelasWali->tahunAkademik?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4">Belum menjadi wali kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
