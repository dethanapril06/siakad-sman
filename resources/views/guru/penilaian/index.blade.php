@extends('layouts.guru')

@section('title', 'Penilaian')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Penilaian</h4>
        <a href="{{ route('guru.penilaian.create', request()->only('mengajar_id')) }}" class="btn btn-primary">
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

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Total Penilaian</small>
                    <h4 class="mb-0">{{ $penilaians->count() }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Sudah Ada Nilai</small>
                    <h4 class="mb-0">{{ $penilaians->filter(fn ($penilaian) => $penilaian->sudah_dinilai)->count() }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Belum Dinilai</small>
                    <h4 class="mb-0">{{ $penilaians->reject(fn ($penilaian) => $penilaian->sudah_dinilai)->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Penilaian</h5>

            <form action="{{ route('guru.penilaian.index') }}" method="GET" class="d-flex gap-2">
                <select name="mengajar_id" class="form-select">
                    <option value="">Semua mengajar</option>
                    @foreach ($mengajars as $mengajar)
                        <option value="{{ $mengajar->id }}" @selected((string) request('mengajar_id') === (string) $mengajar->id)>
                            {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                            - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
                        </option>
                    @endforeach
                </select>
                <select name="jenis_nilai_id" class="form-select">
                    <option value="">Semua jenis</option>
                    @foreach ($jenisNilais as $jenisNilai)
                        <option value="{{ $jenisNilai->id }}" @selected((string) request('jenis_nilai_id') === (string) $jenisNilai->id)>
                            {{ $jenisNilai->nama }}
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
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Nilai</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($penilaians as $penilaian)
                        <tr>
                            <td><strong>{{ $penilaian->judul }}</strong></td>
                            <td>{{ $penilaian->jenisNilai?->nama ?? '-' }}</td>
                            <td>{{ $penilaian->tanggal->format('d M Y') }}</td>
                            <td>{{ $penilaian->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $penilaian->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $penilaian->sudah_dinilai ? 'bg-label-success' : 'bg-label-warning' }}">
                                    {{ $penilaian->jumlah_dinilai }} siswa
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('guru.penilaian.show', $penilaian) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guru.penilaian.edit', $penilaian) }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                            Edit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guru.nilai.edit', $penilaian) }}">
                                            <i class="bx bx-list-check me-1"></i>
                                            Input Nilai
                                        </a>
                                        <form action="{{ route('guru.penilaian.destroy', $penilaian) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="dropdown-item text-danger"
                                                onclick="return confirm('Hapus penilaian ini?')"
                                            >
                                                <i class="bx bx-trash me-1"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Data penilaian belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$penilaians" align="end" />
        </div>
    </div>
@endsection
