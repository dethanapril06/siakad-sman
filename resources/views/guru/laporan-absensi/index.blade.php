@extends('layouts.guru')

@section('title', 'Laporan Absensi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Laporan Absensi</h4>
        @if ($selectedMengajar && $rekapSiswa->isNotEmpty())
            <a href="{{ route('guru.laporan-absensi.cetak', ['mengajar_id' => $selectedMengajarId]) }}" target="_blank" class="btn btn-primary">
                <i class="bx bx-printer me-1"></i> Cetak Laporan Absensi
            </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.laporan-absensi.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label class="form-label" for="mengajar_id">Penugasan Mengajar</label>
                        <select name="mengajar_id" id="mengajar_id" class="form-select">
                            @forelse ($mengajars as $mengajar)
                                <option value="{{ $mengajar->id }}" @selected((string) $selectedMengajarId === (string) $mengajar->id)>
                                    {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                                    - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
                                    - {{ $mengajar->semester?->nama_lengkap ?? '-' }}
                                </option>
                            @empty
                                <option value="">Belum ada data mengajar</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedMengajar)
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Mata Pelajaran</small>
                        <span class="fw-semibold">{{ $selectedMengajar->mataPelajaran?->nama ?? '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kelas</small>
                        <span class="fw-semibold">{{ $selectedMengajar->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Semester</small>
                        <span class="fw-semibold">{{ $selectedMengajar->semester?->nama_lengkap ?? '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Pertemuan</small>
                        <h4 class="mb-0">{{ $totalPertemuan }} Pertemuan</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Tabel Rekapitulasi Absensi Siswa</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Siswa</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Alpa</th>
                        <th class="text-center">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapSiswa as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>
                                <strong>{{ $row['siswa']?->nama ?? '-' }}</strong>
                                <div class="small text-muted">NIS: {{ $row['siswa']?->nis ?? '-' }}</div>
                            </td>
                            <td class="text-center"><span class="badge bg-label-success">{{ $row['hadir'] }}</span></td>
                            <td class="text-center"><span class="badge bg-label-warning">{{ $row['terlambat'] }}</span></td>
                            <td class="text-center"><span class="badge bg-label-info">{{ $row['sakit'] }}</span></td>
                            <td class="text-center"><span class="badge bg-label-primary">{{ $row['izin'] }}</span></td>
                            <td class="text-center"><span class="badge bg-label-danger">{{ $row['alpa'] }}</span></td>
                            <td class="text-center">
                                <strong class="{{ $row['persentase'] >= 75 ? 'text-success' : 'text-danger' }}">
                                    {{ $row['persentase'] }}%
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Data rekap absensi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
