@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.akademik.jadwal' : 'kepala-sekolah.akademik.jadwal';
@endphp

@extends($layout)

@section('title', 'Jadwal')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Jadwal</h4>

        @if ($isPegawaiTu)
            <a href="{{ route('pegawai-tu.akademik.jadwal.create') }}" class="btn btn-primary">
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
            <h5 class="mb-0">Daftar Jadwal</h5>

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
                <select name="kelas_akademik_id" class="form-select">
                    <option value="">Semua kelas</option>
                    @foreach ($kelasAkademiks as $kelasAkademik)
                        <option value="{{ $kelasAkademik->id }}" @selected((string) request('kelas_akademik_id') === (string) $kelasAkademik->id)>
                            {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="hari" class="form-select">
                    <option value="">Semua hari</option>
                    @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
                        <option value="{{ $hari }}" @selected(request('hari') === $hari)>{{ ucfirst($hari) }}</option>
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
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jadwals as $jadwal)
                        <tr>
                            <td><strong>{{ ucfirst($jadwal->hari) }}</strong></td>
                            <td>{{ $jadwal->jam }}</td>
                            <td>{{ $jadwal->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $jadwal->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->mengajar?->guru?->nama ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan?->nama ?? '-' }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $jadwal) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>
                                        @if ($isPegawaiTu)
                                            <a class="dropdown-item"
                                                href="{{ route('pegawai-tu.akademik.jadwal.edit', $jadwal) }}">
                                                <i class="bx bx-edit-alt me-1"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('pegawai-tu.akademik.jadwal.destroy', $jadwal) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Hapus jadwal ini?')">
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
                            <td colspan="7" class="text-center py-4">Data jadwal belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$jadwals" align="end" />
        </div>
    </div>
@endsection
