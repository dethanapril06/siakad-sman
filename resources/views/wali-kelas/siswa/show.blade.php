@extends('layouts.guru')

@section('title', 'Detail Siswa Wali')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas / Siswa /</span> Detail</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('wali-kelas.absensi.show', $siswa) }}" class="btn btn-outline-primary">
                <i class="bx bx-calendar-check me-1"></i>
                Absensi
            </a>
            <a href="{{ route('wali-kelas.nilai.show', $siswa) }}" class="btn btn-outline-primary">
                <i class="bx bx-bar-chart-alt-2 me-1"></i>
                Nilai
            </a>
            <a href="{{ route('wali-kelas.siswa.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Siswa</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><small class="text-muted d-block">NIS</small><span class="fw-semibold">{{ $siswa->nis }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">NISN</small><span class="fw-semibold">{{ $siswa->nisn ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Nama</small><span class="fw-semibold">{{ $siswa->nama }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Kelas</small><span class="fw-semibold">{{ $kelasWali->nama_lengkap }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Jenis Kelamin</small><span class="fw-semibold">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Tempat, Tanggal Lahir</small><span class="fw-semibold">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir?->format('d M Y') ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Nama Orang Tua</small><span class="fw-semibold">{{ $siswa->nama_orang_tua ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">No. HP Orang Tua</small><span class="fw-semibold">{{ $siswa->no_hp_orang_tua ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Status</small><span class="badge {{ $siswa->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">{{ ucfirst($siswa->status) }}</span></div>
                <div class="col-md-12"><small class="text-muted d-block">Alamat</small><span class="fw-semibold">{{ $siswa->alamat ?? '-' }}</span></div>
            </div>
        </div>
    </div>
@endsection
