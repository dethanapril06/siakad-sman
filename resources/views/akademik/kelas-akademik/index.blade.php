@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.akademik.kelas-akademik' : 'kepala-sekolah.akademik.kelas-akademik';
@endphp

@extends($layout)

@section('title', 'Kelas Akademik')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Kelas Akademik</h4>

        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.akademik.kelas-akademik.create') }}" class="btn btn-primary">
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
            <h5 class="mb-0">Daftar Kelas Akademik</h5>

            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-nowrap align-items-center gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari kelas/wali kelas" />
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Semua tahun</option>
                    @foreach ($tahunAkademiks as $tahunAkademik)
                        <option value="{{ $tahunAkademik->id }}" @selected((string) $tahunAkademikId === (string) $tahunAkademik->id)>
                            {{ $tahunAkademik->nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search"></i>
                </button>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tahun Akademik</th>
                        <th>Wali Kelas</th>
                        <th>Anggota</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($kelasAkademiks as $kelasAkademik)
                        <tr>
                            <td><strong>{{ $kelasAkademik->nama_lengkap }}</strong></td>
                            <td>{{ $kelasAkademik->tahunAkademik?->nama ?? '-' }}</td>
                            <td>{{ $kelasAkademik->waliKelas?->nama ?? '-' }}</td>
                            <td>{{ $kelasAkademik->anggota_kelas_count }} siswa</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item"
                                            href="{{ route($routePrefix . '.show', $kelasAkademik) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>

                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.akademik.kelas-akademik.edit', $kelasAkademik) }}">
                                                <i class="bx bx-edit-alt me-1"></i>
                                                Edit
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.akademik.anggota-kelas.index', $kelasAkademik) }}">
                                                <i class="bx bx-group me-1"></i>
                                                Anggota Kelas
                                            </a>
                                            <form
                                                action="{{ route('pegawai-tu.akademik.kelas-akademik.destroy', $kelasAkademik) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus kelas akademik ini?')">
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
                            <td colspan="5" class="text-center py-4">Data kelas akademik belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$kelasAkademiks" align="end" />
        </div>
    </div>
@endsection
