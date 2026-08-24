@extends('layouts.guru')

@section('title', 'Absensi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Absensi</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Total Pertemuan</small>
                    <h4 class="mb-0">{{ $pertemuans->count() }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Sudah Lengkap</small>
                    <h4 class="mb-0">{{ $pertemuans->filter(fn ($pertemuan) => $pertemuan->sudah_diabsen)->count() }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Belum Lengkap</small>
                    <h4 class="mb-0">{{ $pertemuans->reject(fn ($pertemuan) => $pertemuan->sudah_diabsen)->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Absensi Pertemuan</h5>

            <form action="{{ route('guru.absensi.index') }}" method="GET" class="d-flex gap-2">
                <select name="mengajar_id" class="form-select">
                    <option value="">Semua mengajar</option>
                    @foreach ($mengajars as $mengajar)
                        <option value="{{ $mengajar->id }}" @selected((string) request('mengajar_id') === (string) $mengajar->id)>
                            {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                            - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
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
                        <th>Pertemuan</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Progres</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($pertemuans as $pertemuan)
                        <tr>
                            <td><strong>Ke-{{ $pertemuan->pertemuan_ke }}</strong></td>
                            <td>{{ $pertemuan->tanggal->format('d M Y') }}</td>
                            <td>{{ $pertemuan->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $pertemuan->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $pertemuan->progres_absensi }}</td>
                            <td>
                                <span class="badge {{ $pertemuan->sudah_diabsen ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ $pertemuan->sudah_diabsen ? 'Lengkap' : 'Belum lengkap' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('guru.absensi.edit', $pertemuan) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-check-square me-1"></i>
                                    Isi Absensi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Pertemuan belum tersedia untuk absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$pertemuans" align="end" />
        </div>
    </div>
@endsection
