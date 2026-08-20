@extends('layouts.guru')

@section('title', 'Pertemuan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Pertemuan</h4>
        <a href="{{ route('guru.pertemuan.create', request()->only('mengajar_id')) }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>
            Tambah
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Pertemuan</h5>

            <form action="{{ route('guru.pertemuan.index') }}" method="GET" class="d-flex gap-2">
                <select name="mengajar_id" class="form-select">
                    <option value="">Semua mengajar</option>
                    @foreach ($mengajars as $mengajar)
                        <option value="{{ $mengajar->id }}" @selected((string) request('mengajar_id') === (string) $mengajar->id)>
                            {{ $mengajar->mataPelajaran?->nama ?? '-' }} - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
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
                        <th>Absensi</th>
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
                            <td>
                                <span class="badge {{ $pertemuan->sudah_diabsen ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ $pertemuan->progres_absensi }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('guru.pertemuan.show', $pertemuan) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guru.pertemuan.edit', $pertemuan) }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                            Edit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guru.absensi.edit', $pertemuan) }}">
                                            <i class="bx bx-check-square me-1"></i>
                                            Absensi
                                        </a>
                                        <form action="{{ route('guru.pertemuan.destroy', $pertemuan) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="dropdown-item text-danger"
                                                onclick="return confirm('Hapus pertemuan ini?')"
                                            >
                                                <i class="bx bx-trash me-1"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Data pertemuan belum tersedia.</td>
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
