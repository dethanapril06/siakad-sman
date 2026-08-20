@extends('layouts.pegawai-tu')

@section('title', 'Anggota Kelas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Akademik / Kelas Akademik /</span> Anggota Kelas</h4>
            <div class="text-muted">
                {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama ?? '-' }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pegawai-tu.akademik.anggota-kelas.create', $kelasAkademik) }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                Tambah Anggota
            </a>
            <a href="{{ route('pegawai-tu.akademik.kelas-akademik.show', $kelasAkademik) }}"
                class="btn btn-outline-secondary">
                Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <h5 class="card-header">Ringkasan Kelas</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $kelasAkademik->nama_lengkap }}</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Wali Kelas</small>
                    <span class="fw-semibold">{{ $kelasAkademik->waliKelas?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Jumlah Anggota</small>
                    <span class="fw-semibold">{{ $anggotaKelas->total() }} siswa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Anggota</h5>

            <form action="{{ route('pegawai-tu.akademik.anggota-kelas.index', $kelasAkademik) }}" method="GET"
                class="d-flex gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari NIS/NISN/nama" />
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search"></i>
                </button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($anggotaKelas as $anggota)
                        <tr>
                            <td><strong>{{ $anggota->siswa?->nis ?? '-' }}</strong></td>
                            <td>{{ $anggota->siswa?->nisn ?? '-' }}</td>
                            <td>{{ $anggota->siswa?->nama ?? '-' }}</td>
                            <td>{{ $anggota->siswa?->user?->email ?? '-' }}</td>
                            <td>
                                <span
                                    class="badge {{ $anggota->siswa?->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ ucfirst($anggota->siswa?->status ?? '-') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item"
                                            href="{{ route('pegawai-tu.master.siswa.show', $anggota->siswa) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail Siswa
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('pegawai-tu.akademik.anggota-kelas.pindah-form', $anggota) }}">
                                            <i class="bx bx-transfer me-1"></i>
                                            Pindah Kelas
                                        </a>
                                        <form action="{{ route('pegawai-tu.akademik.anggota-kelas.destroy', $anggota) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Keluarkan siswa ini dari kelas?')">
                                                <i class="bx bx-trash me-1"></i>
                                                Keluarkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada anggota kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$anggotaKelas" align="end" />
        </div>
    </div>
@endsection
