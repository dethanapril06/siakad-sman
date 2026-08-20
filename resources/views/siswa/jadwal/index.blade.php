@extends('layouts.siswa')

@section('title', 'Jadwal')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Jadwal</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas Aktif</small>
                    <h4 class="mb-0">{{ $kelasAkademik?->nama_lengkap ?? '-' }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Total Jadwal</small>
                    <h4 class="mb-0">{{ $jadwals->count() }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Semester Dipilih</small>
                    <span class="fw-semibold">
                        {{ $semesters->firstWhere('id', $semesterId)?->nama ? ucfirst($semesters->firstWhere('id', $semesterId)->nama) : '-' }}
                        @if ($semesters->firstWhere('id', $semesterId)?->tahunAkademik)
                            - {{ $semesters->firstWhere('id', $semesterId)->tahunAkademik->nama }}
                        @endif
                    </span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Hari Dipilih</small>
                    <span class="fw-semibold">{{ request('hari') ? ucfirst(request('hari')) : 'Semua hari' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Jadwal Pelajaran</h5>

            <form action="{{ route('siswa.jadwal.index') }}" method="GET"
                class="d-flex flex-nowrap align-items-center gap-2">
                <select name="semester_id" class="form-select">
                    <option value="">Semester aktif</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                            {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="hari" class="form-select">
                    <option value="">Semua hari</option>
                    @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
                        <option value="{{ $hari }}" @selected(request('hari') === $hari)>{{ ucfirst($hari) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-filter-alt"></i>
                </button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jadwals as $jadwal)
                        <tr>
                            <td><strong>{{ ucfirst($jadwal->hari) }}</strong></td>
                            <td>{{ $jadwal->jam }}</td>
                            <td>{{ $jadwal->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->mengajar?->guru?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                            <td>
                                {{ ucfirst($jadwal->mengajar?->semester?->nama ?? '-') }}
                                - {{ $jadwal->mengajar?->semester?->tahunAkademik?->nama ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                {{ $kelasAkademik ? 'Jadwal pelajaran belum tersedia.' : 'Kelas aktif belum tersedia.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$jadwals" align="end" />
        </div>
    </div>
@endsection
