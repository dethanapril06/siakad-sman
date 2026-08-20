@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = 'layouts.pegawai-tu';
    $routePrefix = 'pegawai-tu.master.pegawai-tu';
@endphp

@extends($layout)

@section('title', 'Pegawai Tata Usaha')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Pegawai Tata Usaha</h4>

        <a href="{{ route('pegawai-tu.master.pegawai-tu.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>
            Tambah Pegawai TU
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Pegawai Tata Usaha</h5>

            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-wrap align-items-center gap-2">
                <input type="text" name="search" class="form-control" style="width: auto; min-width: 200px;"
                    value="{{ request('search') }}" placeholder="Cari NIP / nama / email" />
                <select name="jenis_kelamin" class="form-select" style="width: auto;">
                    <option value="">Semua jenis kelamin</option>
                    <option value="L" @selected(request('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(request('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
                <button type="submit" class="btn btn-outline-primary" title="Cari">
                    <i class="bx bx-search"></i>
                </button>
                @if (request()->hasAny(['search', 'jenis_kelamin']))
                    <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bx bx-refresh"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Email</th>
                        <th>Jenis Kelamin</th>
                        <th>No. HP</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($pegawaiTus as $pegawaiTu)
                        <tr>
                            <td><strong>{{ $pegawaiTu->nip ?: '-' }}</strong></td>
                            <td>
                                <strong>{{ $pegawaiTu->nama }}</strong>
                                @if (auth()->id() === $pegawaiTu->user_id)
                                    <span class="badge bg-label-info ms-1">Anda</span>
                                @endif
                            </td>
                            <td>{{ $pegawaiTu->user?->email ?? '-' }}</td>
                            <td>{{ $pegawaiTu->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $pegawaiTu->no_hp ?: '-' }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $pegawaiTu) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>

                                        <a class="dropdown-item" href="{{ route($routePrefix . '.edit', $pegawaiTu) }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                            Edit
                                        </a>

                                        <form action="{{ route('pegawai-tu.master.pegawai-tu.reset-password', $pegawaiTu) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item"
                                                onclick="return confirm('Reset password akun Pegawai TU {{ $pegawaiTu->nama }} menjadi default (password)?')">
                                                <i class="bx bx-key me-1"></i>
                                                Reset Password
                                            </button>
                                        </form>

                                        @if (auth()->id() !== $pegawaiTu->user_id)
                                            <form action="{{ route($routePrefix . '.destroy', $pegawaiTu) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus data Pegawai TU ini?')">
                                                    <i class="bx bx-trash me-1"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Data Pegawai TU belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$pegawaiTus" align="end" />
        </div>
    </div>
@endsection
