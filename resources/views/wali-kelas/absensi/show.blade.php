@extends('layouts.guru')

@section('title', 'Detail Absensi Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas / Absensi /</span> Detail</h4>
        <a href="{{ route('wali-kelas.absensi.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><small class="text-muted d-block">Siswa</small><h4 class="mb-0">{{ $siswa->nama }}</h4></div>
                <div class="col-md-3"><small class="text-muted d-block">Total</small><h4 class="mb-0">{{ $rekap['total'] }}</h4></div>
                <div class="col-md-3"><small class="text-muted d-block">Hadir</small><h4 class="mb-0">{{ $rekap['hadir'] + $rekap['terlambat'] }}</h4></div>
                <div class="col-md-3"><small class="text-muted d-block">Kehadiran</small><h4 class="mb-0">{{ $rekap['persentase_kehadiran'] }}%</h4></div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Riwayat Absensi</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($absensis as $absensi)
                        <tr>
                            <td>{{ $absensi->pertemuan?->tanggal?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $absensi->pertemuan?->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $absensi->pertemuan?->mengajar?->guru?->nama ?? '-' }}</td>
                            <td>{{ ucfirst($absensi->status) }}</td>
                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Riwayat absensi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
