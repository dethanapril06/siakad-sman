@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.master.tahun-akademik' : 'kepala-sekolah.master.tahun-akademik';
@endphp

@extends($layout)

@section('title', 'Tahun Akademik')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Tahun Akademik</h4>

        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.master.tahun-akademik.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                Tambah
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
            <h5 class="mb-0">Daftar Tahun Akademik</h5>

            <form action="{{ route($routePrefix . '.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari tahun akademik" />
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search"></i>
                </button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($tahunAkademiks as $tahunAkademik)
                        <tr>
                            <td><strong>{{ $tahunAkademik->nama }}</strong></td>
                            <td>{{ $tahunAkademik->tanggal_mulai->format('d M Y') }}</td>
                            <td>{{ $tahunAkademik->tanggal_selesai->format('d M Y') }}</td>
                            <td>
                                @if ($tahunAkademik->is_active)
                                    <span class="badge bg-label-success me-1">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $tahunAkademik) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>

                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.master.tahun-akademik.edit', $tahunAkademik) }}">
                                                <i class="bx bx-edit-alt me-1"></i>
                                                Edit
                                            </a>
                                            <form
                                                action="{{ route('pegawai-tu.master.tahun-akademik.destroy', $tahunAkademik) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus tahun akademik ini?')">
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
                            <td colspan="5" class="text-center py-4">Data tahun akademik belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$tahunAkademiks" align="end" />
        </div>
    </div>
@endsection
