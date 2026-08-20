@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.akademik.mengajar.index' : 'kepala-sekolah.akademik.mengajar.index';
@endphp

@extends($layout)

@section('title', 'Detail Mengajar')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik / Mengajar /</span> Detail</h4>

        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.akademik.mengajar.edit', $mengajar) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Mengajar</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Guru</small>
                    <span class="fw-semibold">{{ $mengajar->guru?->nama ?? '-' }}</span>
                </div>
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
                        {{ ucfirst($mengajar->semester?->nama ?? '-') }} - {{ $mengajar->semester?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Jadwal Terkait</h5>
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
                            <td>{{ ucfirst($jadwal->hari) }}</td>
                            <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                            <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Belum ada jadwal terkait.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
