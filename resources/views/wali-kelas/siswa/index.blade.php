@extends('layouts.guru')

@section('title', 'Siswa Wali')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas /</span> Siswa</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kelas Wali</small>
                    <h4 class="mb-0">{{ $kelasWali->nama_lengkap }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Tahun Akademik</small>
                    <span class="fw-semibold">{{ $kelasWali->tahunAkademik?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Jumlah Siswa</small>
                    <h4 class="mb-0">{{ $siswas->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Siswa Wali</h5>
            <form action="{{ route('wali-kelas.siswa.index') }}" method="GET" class="d-flex flex-nowrap align-items-center gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari NIS/NISN/nama" />
                <select name="jenis_kelamin" class="form-select">
                    <option value="">Semua JK</option>
                    <option value="L" @selected(request('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(request('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    @foreach (['aktif', 'lulus', 'pindah', 'nonaktif'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i></button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($siswas as $siswa)
                        <tr>
                            <td><strong>{{ $siswa->nis }}</strong></td>
                            <td>{{ $siswa->nama }}</td>
                            <td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>
                                <span class="badge {{ $siswa->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('wali-kelas.siswa.show', $siswa) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Data siswa wali belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$siswas" align="end" />
        </div>
    </div>
@endsection
