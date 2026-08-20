@extends('layouts.guru')

@section('title', 'Mengajar')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Mengajar</h4>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Mengajar Saya</h5>

            <form action="{{ route('guru.mengajar.index') }}" method="GET" class="d-flex gap-2">
                <select name="semester_id" class="form-select">
                    <option value="">Semua semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                            {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                        </option>
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
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Semester</th>
                        <th>Jadwal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($mengajars as $mengajar)
                        <tr>
                            <td><strong>{{ $mengajar->mataPelajaran?->nama ?? '-' }}</strong></td>
                            <td>{{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>
                                {{ ucfirst($mengajar->semester?->nama ?? '-') }}
                                - {{ $mengajar->semester?->tahunAkademik?->nama ?? '-' }}
                            </td>
                            <td>
                                @forelse ($mengajar->jadwals as $jadwal)
                                    <div>{{ ucfirst($jadwal->hari) }}, {{ $jadwal->jam }}</div>
                                @empty
                                    <span class="text-muted">Belum ada jadwal</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('guru.mengajar.show', $mengajar) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Data mengajar belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$mengajars" align="end" />
        </div>
    </div>
@endsection
