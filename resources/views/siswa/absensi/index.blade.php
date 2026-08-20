@extends('layouts.siswa')

@section('title', 'Absensi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Absensi</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas Aktif</small>
                    <h4 class="mb-0">{{ $kelasAkademik?->nama_lengkap ?? '-' }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Total Absensi</small>
                    <h4 class="mb-0">{{ $rekap['total'] }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Hadir + Terlambat</small>
                    <h4 class="mb-0">{{ $rekap['hadir'] + $rekap['terlambat'] }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Persentase Kehadiran</small>
                    <h4 class="mb-0">{{ $rekap['persentase_kehadiran'] }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Hadir</span>
                    <h3 class="mb-0">{{ $rekap['hadir'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Sakit</span>
                    <h3 class="mb-0">{{ $rekap['sakit'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Izin</span>
                    <h3 class="mb-0">{{ $rekap['izin'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Alpa</span>
                    <h3 class="mb-0">{{ $rekap['alpa'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Terlambat</span>
                    <h3 class="mb-0">{{ $rekap['terlambat'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Absensi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.absensi.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="semester_id">Semester</label>
                        <select name="semester_id" id="semester_id" class="form-select">
                            <option value="">Semester aktif</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                                    {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="mata_pelajaran_id">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select">
                            <option value="">Semua mapel</option>
                            @foreach ($mataPelajarans as $mataPelajaran)
                                <option value="{{ $mataPelajaran->id }}" @selected((string) $mataPelajaranId === (string) $mataPelajaran->id)>
                                    {{ $mataPelajaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua status</option>
                            @foreach (['hadir', 'sakit', 'izin', 'alpa', 'terlambat'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="tanggal_mulai">Dari</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $tanggalMulai?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="tanggal_selesai">Sampai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Rekap Per Mata Pelajaran</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Total</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Terlambat</th>
                        <th>Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapPerMataPelajaran as $rekapMapel)
                        <tr>
                            <td><strong>{{ $rekapMapel['mata_pelajaran']?->nama ?? '-' }}</strong></td>
                            <td>{{ $rekapMapel['guru']?->nama ?? '-' }}</td>
                            <td>{{ $rekapMapel['total'] }}</td>
                            <td>{{ $rekapMapel['hadir'] }}</td>
                            <td>{{ $rekapMapel['sakit'] }}</td>
                            <td>{{ $rekapMapel['izin'] }}</td>
                            <td>{{ $rekapMapel['alpa'] }}</td>
                            <td>{{ $rekapMapel['terlambat'] }}</td>
                            <td>
                                <span class="badge {{ $rekapMapel['persentase_kehadiran'] >= 80 ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ $rekapMapel['persentase_kehadiran'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Rekap absensi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                        <th>Pertemuan</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($paginatedAbsensis as $absensi)
                        <tr>
                            <td>{{ $absensi->pertemuan?->tanggal?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $absensi->pertemuan?->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $absensi->pertemuan?->mengajar?->guru?->nama ?? '-' }}</td>
                            <td>Ke-{{ $absensi->pertemuan?->pertemuan_ke ?? '-' }}</td>
                            <td>
                                <span class="badge {{ in_array($absensi->status, ['hadir', 'terlambat'], true) ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ ucfirst($absensi->status) }}
                                </span>
                            </td>
                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                {{ $kelasAkademik ? 'Riwayat absensi belum tersedia.' : 'Kelas aktif belum tersedia.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$paginatedAbsensis" align="end" />
        </div>
    </div>
@endsection
