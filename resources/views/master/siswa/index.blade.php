@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.master.siswa' : 'kepala-sekolah.master.siswa';
@endphp

@extends($layout)

@section('title', 'Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Siswa</h4>
        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.master.siswa.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Daftar Siswa</h5>
            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-nowrap align-items-center gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari NIS/NISN/nama" />
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    @foreach (['aktif', 'lulus', 'pindah', 'nonaktif'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="jurusan_id" class="form-select">
                    <option value="">Semua jurusan</option>
                    @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" @selected((string) request('jurusan_id') === (string) $jurusan->id)>
                            {{ $jurusan->nama }}
                        </option>
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
                        <th>Email</th>
                        <th>Kelas Aktif</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($siswas as $siswa)
                        @php($kelasAktif = $siswa->anggotaKelas->first()?->kelasAkademik?->kelas)
                        <tr>
                            <td><strong>{{ $siswa->nis }}</strong></td>
                            <td>{{ $siswa->nama }}</td>
                            <td>{{ $siswa->user?->email ?? '-' }}</td>
                            <td>{{ $kelasAktif?->nama_lengkap ?? '-' }}</td>
                            <td><span
                                    class="badge {{ $siswa->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">{{ ucfirst($siswa->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $siswa) }}"><i
                                                class="bx bx-show me-1"></i> Detail</a>
                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.master.siswa.edit', $siswa) }}"><i
                                                    class="bx bx-edit-alt me-1"></i> Edit</a>
                                            <form action="{{ route('pegawai-tu.master.siswa.reset-password', $siswa) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item"
                                                    onclick="return confirm('Reset password akun siswa {{ $siswa->nama }} menjadi default (password)?')">
                                                    <i class="bx bx-key me-1"></i>
                                                    Reset Password
                                                </button>
                                            </form>
                                            <form action="{{ route('pegawai-tu.master.siswa.destroy', $siswa) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus data siswa ini?')">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Data siswa belum tersedia.</td>
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
