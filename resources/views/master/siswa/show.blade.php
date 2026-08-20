@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $indexRoute = $isPegawaiTu ? 'pegawai-tu.master.siswa.index' : 'kepala-sekolah.master.siswa.index';
@endphp

@extends($layout)

@section('title', 'Detail Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master / Siswa /</span> Detail</h4>
        <div class="d-flex gap-2">
            @if ($isPegawaiTu)
                <a href="{{ route('pegawai-tu.master.siswa.edit', $siswa) }}" class="btn btn-primary"><i class="bx bx-edit-alt me-1"></i> Edit</a>
            @endif
            <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Siswa</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><small class="text-muted d-block">NIS</small><span class="fw-semibold">{{ $siswa->nis }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">NISN</small><span class="fw-semibold">{{ $siswa->nisn ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Nama</small><span class="fw-semibold">{{ $siswa->nama }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Email Login</small><span class="fw-semibold">{{ $siswa->user?->email ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Jenis Kelamin</small><span class="fw-semibold">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Tempat, Tanggal Lahir</small><span class="fw-semibold">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir?->format('d M Y') ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Nama Orang Tua</small><span class="fw-semibold">{{ $siswa->nama_orang_tua ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">No. HP Orang Tua</small><span class="fw-semibold">{{ $siswa->no_hp_orang_tua ?? '-' }}</span></div>
                <div class="col-md-6"><small class="text-muted d-block">Status</small><span class="badge {{ $siswa->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">{{ ucfirst($siswa->status) }}</span></div>
                <div class="col-md-12"><small class="text-muted d-block">Alamat</small><span class="fw-semibold">{{ $siswa->alamat ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Riwayat Kelas</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead><tr><th>Kelas</th><th>Tahun Akademik</th></tr></thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($siswa->anggotaKelas as $anggota)
                        <tr>
                            <td>{{ $anggota->kelasAkademik?->kelas?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $anggota->kelasAkademik?->tahunAkademik?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-4">Belum memiliki riwayat kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
