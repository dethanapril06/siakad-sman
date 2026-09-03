@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.laporan.nilai' : 'kepala-sekolah.laporan.nilai';
@endphp

@extends($layout)

@section('title', 'Laporan Nilai')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Nilai</h4>
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
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Filter Laporan Nilai</h5>
            <span class="badge bg-label-primary">KKM {{ $kkm }}</span>
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
                    <div class="col-md-9">
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
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <small class="text-muted d-block mb-1">Bobot nilai akhir</small>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-label-secondary">Harian {{ $bobot['NH'] }}%</span>
                    <span class="badge bg-label-secondary">Tugas {{ $bobot['TUGAS'] }}%</span>
                    <span class="badge bg-label-secondary">UTS {{ $bobot['UTS'] }}%</span>
                    <span class="badge bg-label-secondary">UAS {{ $bobot['UAS'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    @php
        $rataGlobal = $laporanNilai
            ->pluck('rata_rata_kelas')
            ->filter(fn ($nilai) => $nilai !== null);
    @endphp

    <div class="row">
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Kelas</span>
                    <h3 class="mb-0">{{ $laporanNilai->count() }}</h3>
                    <small class="text-muted">Sesuai filter</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Siswa</span>
                    <h3 class="mb-0">{{ $laporanNilai->sum('jumlah_siswa') }}</h3>
                    <small class="text-muted">Akumulasi kelas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Mapel</span>
                    <h3 class="mb-0">{{ $laporanNilai->sum('jumlah_mapel') }}</h3>
                    <small class="text-muted">Akumulasi kelas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Rata-rata Global</span>
                    <h3 class="mb-0">{{ $rataGlobal->isNotEmpty() ? number_format((float) $rataGlobal->avg(), 2) : '-' }}</h3>
                    <small class="text-muted">Rata-rata kelas</small>
                </div>
            </div>
        </div>
    </div>

    @forelse ($laporanNilai as $laporan)
        @php($kelasAkademik = $laporan['kelas_akademik'])
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">Nilai Akhir Per Mata Pelajaran</h5>
                    <small class="text-muted">
                        {{ $kelasAkademik?->nama_lengkap ?? '-' }}
                        | {{ $kelasAkademik?->tahunAkademik?->nama ?? '-' }}
                    </small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-label-primary">{{ $laporan['jumlah_siswa'] }} siswa</span>
                    <span class="badge bg-label-info">{{ $laporan['jumlah_mapel'] }} mapel</span>
                    <span class="badge bg-label-success">
                        Rata-rata {{ $laporan['rata_rata_kelas'] !== null ? number_format($laporan['rata_rata_kelas'], 2) : '-' }}
                    </span>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            @foreach ($laporan['mata_pelajarans'] as $mataPelajaran)
                                <th>{{ $mataPelajaran->nama }}</th>
                            @endforeach
                            <th>Rata-rata</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($laporan['raport_nilai'] as $row)
                            <tr>
                                <td>{{ $row['no'] }}</td>
                                <td><strong>{{ $row['siswa']?->nis ?? '-' }}</strong></td>
                                <td>{{ $row['siswa']?->nama ?? '-' }}</td>
                                @foreach ($laporan['mata_pelajarans'] as $mataPelajaran)
                                    @php($rekap = $row['nilai_mapel']->get($mataPelajaran->id))
                                    <td>
                                        {{ $rekap['nilai_akhir'] !== null ? number_format($rekap['nilai_akhir'], 2) : '-' }}
                                    </td>
                                @endforeach
                                <td>
                                    <strong>{{ $row['rata_rata'] !== null ? number_format($row['rata_rata'], 2) : '-' }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $row['keterangan'] === 'Tuntas' ? 'bg-label-success' : ($row['keterangan'] === 'Belum Tuntas' ? 'bg-label-danger' : 'bg-label-secondary') }}">
                                        {{ $row['keterangan'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 5 + $laporan['mata_pelajarans']->count() }}" class="text-center py-4">Data raport belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-4">Data laporan nilai belum tersedia.</div>
        </div>
    @endforelse

    <div class="d-flex justify-content-end my-4">
        <x-pagination :paginator="$kelasLaporan" align="end" />
    </div>
@endsection
