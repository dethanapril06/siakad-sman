@extends('layouts.guru')

@section('title', 'Absensi Wali')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas /</span> Absensi</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kelas Wali</small>
                    <h4 class="mb-0">{{ $kelasWali->nama_lengkap }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ $semesters->firstWhere('id', $semesterId)?->nama ? ucfirst($semesters->firstWhere('id', $semesterId)->nama) : '-' }}
                        - {{ $semesters->firstWhere('id', $semesterId)?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Jumlah Siswa</small>
                    <h4 class="mb-0">{{ $rekapAbsensi->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Rekap Absensi Siswa</h5>
            <form action="{{ route('wali-kelas.absensi.index') }}" method="GET" class="d-flex flex-nowrap align-items-center gap-2">
                <select name="semester_id" class="form-select">
                    <option value="">Semester aktif</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                            {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="mata_pelajaran_id" class="form-select">
                    <option value="">Semua mapel</option>
                    @foreach ($mataPelajarans as $mataPelajaran)
                        <option value="{{ $mataPelajaran->id }}" @selected((string) $mataPelajaranId === (string) $mataPelajaran->id)>
                            {{ $mataPelajaran->nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="bx bx-filter-alt"></i></button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Total</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Terlambat</th>
                        <th>Kehadiran</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($paginatedRekapAbsensi as $rekap)
                        <tr>
                            <td><strong>{{ $rekap['siswa']?->nama ?? '-' }}</strong></td>
                            <td>{{ $rekap['total'] }}</td>
                            <td>{{ $rekap['hadir'] }}</td>
                            <td>{{ $rekap['sakit'] }}</td>
                            <td>{{ $rekap['izin'] }}</td>
                            <td>{{ $rekap['alpa'] }}</td>
                            <td>{{ $rekap['terlambat'] }}</td>
                            <td>
                                <span class="badge {{ $rekap['persentase_kehadiran'] >= 80 ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ $rekap['persentase_kehadiran'] }}%
                                </span>
                            </td>
                            <td class="text-end">
                                @if ($rekap['siswa'])
                                    <a href="{{ route('wali-kelas.absensi.show', $rekap['siswa']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show me-1"></i>
                                        Detail
                                    </a>
                                @endif
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

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$paginatedRekapAbsensi" align="end" />
        </div>
    </div>
@endsection
