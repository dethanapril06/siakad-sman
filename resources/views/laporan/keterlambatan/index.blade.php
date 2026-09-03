@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.laporan.keterlambatan' : 'kepala-sekolah.laporan.keterlambatan';
@endphp

@extends($layout)

@section('title', 'Laporan Keterlambatan')

@section('content')
    @php
        $siswaPalingSering = $ringkasan['siswa_paling_sering_terlambat'] ?? null;
        $tanggalMulaiValue = $tanggalMulai ? $tanggalMulai->format('Y-m-d') : null;
        $tanggalSelesaiValue = $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : null;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Keterlambatan</h4>
        <div class="d-flex gap-2">
            <a href="{{ route($routePrefix . '.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bx bx-printer me-1"></i> Cetak PDF
            </a>
            <a href="{{ route($routePrefix . '.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Laporan Keterlambatan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route($routePrefix . '.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="tahun_akademik_id">Tahun Akademik</label>
                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select">
                            <option value="">Tahun aktif</option>
                            @foreach ($tahunAkademiks as $tahunAkademik)
                                <option value="{{ $tahunAkademik->id }}" @selected((string) $tahunAkademikId === (string) $tahunAkademik->id)>
                                    {{ $tahunAkademik->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="semester_id">Semester</label>
                        <select name="semester_id" id="semester_id" class="form-select">
                            <option value="">Semester aktif</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                                    {{ $semester->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="kelas_akademik_id">Kelas</label>
                        <select name="kelas_akademik_id" id="kelas_akademik_id" class="form-select">
                            <option value="">Semua kelas</option>
                            @foreach ($kelasAkademiks as $kelasAkademik)
                                <option value="{{ $kelasAkademik->id }}" @selected((string) $kelasAkademikId === (string) $kelasAkademik->id)>
                                    {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="mata_pelajaran_id">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select">
                            <option value="">Semua mapel</option>
                            @foreach ($mataPelajarans as $mataPelajaran)
                                <option value="{{ $mataPelajaran->id }}" @selected((string) $mataPelajaranId === (string) $mataPelajaran->id)>
                                    {{ $mataPelajaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="guru_id">Guru</label>
                        <select name="guru_id" id="guru_id" class="form-select">
                            <option value="">Semua guru</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->id }}" @selected((string) $guruId === (string) $guru->id)>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $tanggalMulaiValue }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ $tanggalSelesaiValue }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Keterlambatan</span>
                    <h3 class="mb-0">{{ $ringkasan['total_keterlambatan'] }}</h3>
                    <small class="text-muted">Sesuai filter</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Siswa Terlambat</span>
                    <h3 class="mb-0">{{ $ringkasan['jumlah_siswa_terlambat'] }}</h3>
                    <small class="text-muted">Siswa unik</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Paling Sering Terlambat</span>
                    <h5 class="mb-1">{{ $siswaPalingSering['siswa']?->nama ?? '-' }}</h5>
                    <small class="text-muted">
                        {{ $siswaPalingSering ? $siswaPalingSering['jumlah_terlambat'] . ' kali' : 'Belum ada data' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Siswa Terlambat</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jumlah Terlambat</th>
                        <th>Terakhir Terlambat</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($rekapSiswa as $rekap)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $rekap['siswa']?->nis ?? '-' }}</strong></td>
                            <td>{{ $rekap['siswa']?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-warning">{{ $rekap['jumlah_terlambat'] }} kali</span>
                            </td>
                            <td>{{ $rekap['terakhir_terlambat']?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Rekap keterlambatan siswa belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Detail Keterlambatan</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Pertemuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($detailKeterlambatans as $keterlambatan)
                        @php($mengajar = $keterlambatan->pertemuan?->mengajar)
                        <tr>
                            <td>{{ $keterlambatan->pertemuan?->tanggal?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <strong>{{ $keterlambatan->siswa?->nama ?? '-' }}</strong>
                                <div class="small text-muted">{{ $keterlambatan->siswa?->nis ?? '-' }}</div>
                            </td>
                            <td>{{ $mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                            <td>{{ $mengajar?->guru?->nama ?? '-' }}</td>
                            <td>Pertemuan {{ $keterlambatan->pertemuan?->pertemuan_ke ?? '-' }}</td>
                            <td>{{ $keterlambatan->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Data keterlambatan belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$detailKeterlambatans" align="end" />
        </div>
    </div>
@endsection
