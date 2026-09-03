@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.akademik.mengajar' : 'kepala-sekolah.akademik.mengajar';
@endphp

@extends($layout)

@section('title', 'Mengajar')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Mengajar</h4>

        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.akademik.mengajar.create') }}" class="btn btn-primary">
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
            <h5 class="mb-0">Daftar Penugasan Mengajar</h5>

            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-nowrap align-items-center gap-2">
                <select name="semester_id" class="form-select">
                    <option value="">Semua semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                            {{ $semester->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
                <select name="guru_id" class="form-select">
                    <option value="">Semua guru</option>
                    @foreach ($gurus as $guru)
                        <option value="{{ $guru->id }}" @selected((string) request('guru_id') === (string) $guru->id)>{{ $guru->nama }}</option>
                    @endforeach
                </select>
                <select name="kelas_akademik_id" class="form-select">
                    <option value="">Semua kelas</option>
                    @foreach ($kelasAkademiks as $kelasAkademik)
                        <option value="{{ $kelasAkademik->id }}" @selected((string) request('kelas_akademik_id') === (string) $kelasAkademik->id)>
                            {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="mata_pelajaran_id" class="form-select">
                    <option value="">Semua mapel</option>
                    @foreach ($mataPelajarans as $mataPelajaran)
                        <option value="{{ $mataPelajaran->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mataPelajaran->id)>
                            {{ $mataPelajaran->nama }}
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
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Semester</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($mengajars as $mengajar)
                        <tr>
                            <td><strong>{{ $mengajar->guru?->nama ?? '-' }}</strong></td>
                            <td>{{ $mengajar->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ ucfirst($mengajar->semester?->nama ?? '-') }} -
                                {{ $mengajar->semester?->tahunAkademik?->nama ?? '-' }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $mengajar) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>
                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.akademik.mengajar.edit', $mengajar) }}">
                                                <i class="bx bx-edit-alt me-1"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('pegawai-tu.akademik.mengajar.destroy', $mengajar) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus penugasan mengajar ini?')">
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
                            <td colspan="5" class="text-center py-4">Data penugasan mengajar belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$mengajars" align="end" />
        </div>
    </div>
@endsection
