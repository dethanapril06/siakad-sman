@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.master.kelas' : 'kepala-sekolah.master.kelas';
@endphp

@extends($layout)

@section('title', 'Kelas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Kelas</h4>
        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.master.kelas.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i>
                Tambah</a>
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
            <h5 class="mb-0">Daftar Kelas</h5>
            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-nowrap align-items-center gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari kelas/jurusan" />
                <select name="tingkat" class="form-select">
                    <option value="">Semua tingkat</option>
                    @foreach (['X', 'XI', 'XII'] as $tingkat)
                        <option value="{{ $tingkat }}" @selected(request('tingkat') === $tingkat)>{{ $tingkat }}</option>
                    @endforeach
                </select>
                <select name="jurusan_id" class="form-select">
                    <option value="">Semua jurusan</option>
                    @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" @selected((string) request('jurusan_id') === (string) $jurusan->id)>{{ $jurusan->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i></button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($kelas as $item)
                        <tr>
                            <td><strong>{{ $item->nama_lengkap }}</strong></td>
                            <td>{{ $item->tingkat }}</td>
                            <td>{{ $item->jurusan?->nama ?? '-' }}</td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $item) }}"><i
                                                class="bx bx-show me-1"></i> Detail</a>
                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.master.kelas.edit', $item) }}"><i
                                                    class="bx bx-edit-alt me-1"></i> Edit</a>
                                            <form action="{{ route('pegawai-tu.master.kelas.destroy', $item) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus data kelas ini?')">
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
                            <td colspan="5" class="text-center py-4">Data kelas belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$kelas" align="end" />
        </div>
    </div>
@endsection
