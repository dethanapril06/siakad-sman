@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.master.semester' : 'kepala-sekolah.master.semester';
@endphp

@extends($layout)

@section('title', 'Semester')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Semester</h4>

        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.master.semester.create') }}" class="btn btn-primary">
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
            <h5 class="mb-0">Daftar Semester</h5>

            <form action="{{ route($routePrefix . '.index') }}" method="GET" class="d-flex gap-2">
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Semua tahun</option>
                    @foreach ($tahunAkademiks as $tahunAkademik)
                        <option value="{{ $tahunAkademik->id }}" @selected((string) request('tahun_akademik_id') === (string) $tahunAkademik->id)>
                            {{ $tahunAkademik->nama }}
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
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Akses Cetak Rapor</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($semesters as $semester)
                        <tr>
                            <td><strong>{{ ucfirst($semester->nama) }}</strong></td>
                            <td>{{ $semester->tahunAkademik?->nama ?? '-' }}</td>
                            <td>{{ $semester->tanggal_mulai->format('d M Y') }}</td>
                            <td>{{ $semester->tanggal_selesai->format('d M Y') }}</td>
                            <td>
                                @if ($semester->is_active)
                                    <span class="badge bg-label-success me-1">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($semester->is_rapor_open)
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-lock-open-alt me-1"></i> Terbuka
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-lock-alt me-1"></i> Terkunci
                                        </span>
                                    @endif

                                    @if ($isPegawaiTu)
                                        <form action="{{ route('pegawai-tu.master.semester.toggle-rapor', $semester) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-xs {{ $semester->is_rapor_open ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                    title="{{ $semester->is_rapor_open ? 'Kunci Akses Rapor' : 'Buka Akses Rapor' }}"
                                                    onclick="return confirm('Apakah Anda yakin ingin {{ $semester->is_rapor_open ? 'mengunci' : 'membuka' }} akses cetak rapor untuk semester {{ ucfirst($semester->nama) }}?')">
                                                <i class="bx {{ $semester->is_rapor_open ? 'bx-lock-alt' : 'bx-lock-open-alt' }} me-1"></i>
                                                {{ $semester->is_rapor_open ? 'Kunci' : 'Buka' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                @if ($semester->tanggal_rapor)
                                    <small class="text-muted d-block mt-1">
                                        Tgl: {{ $semester->tanggal_rapor->format('d/m/Y') }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $semester) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>

                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.master.semester.edit', $semester) }}">
                                                <i class="bx bx-edit-alt me-1"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('pegawai-tu.master.semester.destroy', $semester) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus semester ini?')">
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
                            <td colspan="7" class="text-center py-4">Data semester belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$semesters" align="end" />
        </div>
    </div>
@endsection
