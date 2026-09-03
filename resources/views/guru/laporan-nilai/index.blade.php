@extends('layouts.guru')

@section('title', 'Laporan Nilai')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran /</span> Laporan Nilai</h4>
        @if ($selectedMengajar && $laporanNilai->isNotEmpty())
            <a href="{{ route('guru.laporan-nilai.cetak', ['mengajar_id' => $selectedMengajarId]) }}" target="_blank" class="btn btn-primary">
                <i class="bx bx-printer me-1"></i> Cetak Laporan Nilai
            </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Filter Laporan</h5>
            <span class="badge bg-label-primary">KKM {{ $kkm }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.laporan-nilai.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label class="form-label" for="mengajar_id">Penugasan Mengajar</label>
                        <select name="mengajar_id" id="mengajar_id" class="form-select">
                            @forelse ($mengajars as $mengajar)
                                <option value="{{ $mengajar->id }}" @selected((string) $selectedMengajarId === (string) $mengajar->id)>
                                    {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                                    - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
                                    - {{ $mengajar->semester?->nama_lengkap ?? '-' }}
                                </option>
                            @empty
                                <option value="">Belum ada data mengajar</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt me-1"></i>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <small class="text-muted d-block mb-1">Bobot Perhitungan Nilai Rata-rata</small>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-label-secondary">Nilai Harian {{ $bobot['NH'] ?? 20 }}%</span>
                    <span class="badge bg-label-secondary">Tugas {{ $bobot['TUGAS'] ?? 20 }}%</span>
                    <span class="badge bg-label-secondary">Keterampilan {{ $bobot['KTR'] ?? 20 }}%</span>
                    <span class="badge bg-label-secondary">UTS {{ $bobot['UTS'] ?? 20 }}%</span>
                    <span class="badge bg-label-secondary">UAS {{ $bobot['UAS'] ?? 20 }}%</span>
                </div>
            </div>
        </div>
    </div>

    @if ($selectedMengajar)
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Mata Pelajaran</small>
                        <span class="fw-semibold">{{ $selectedMengajar->mataPelajaran?->nama ?? '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kelas</small>
                        <span class="fw-semibold">{{ $selectedMengajar->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Semester</small>
                        <span class="fw-semibold">
                            {{ $selectedMengajar->semester?->nama_lengkap ?? '-' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Jumlah Siswa</small>
                        <h4 class="mb-0">{{ $laporanNilai->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Tabel Laporan Nilai Siswa</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Harian<br><small class="text-muted">Rata-rata</small></th>
                        <th>Tugas<br><small class="text-muted">Rata-rata</small></th>
                        <th>Keterampilan<br><small class="text-muted">Rata-rata</small></th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Nilai Akhir<br><small class="text-muted">Rata-rata</small></th>
                        <th>KKM</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($paginatedLaporanNilai as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>
                                <strong>{{ $row['siswa']?->nama ?? '-' }}</strong>
                                <div class="small text-muted">NIS: {{ $row['siswa']?->nis ?? '-' }}</div>
                            </td>
                            <td>{{ $row['nilai_harian'] !== null ? number_format($row['nilai_harian'], 2) : '-' }}</td>
                            <td>{{ $row['nilai_tugas'] !== null ? number_format($row['nilai_tugas'], 2) : '-' }}</td>
                            <td>{{ $row['nilai_keterampilan'] !== null ? number_format($row['nilai_keterampilan'], 2) : '-' }}</td>
                            <td>{{ $row['nilai_uts'] !== null ? number_format($row['nilai_uts'], 2) : '-' }}</td>
                            <td>{{ $row['nilai_uas'] !== null ? number_format($row['nilai_uas'], 2) : '-' }}</td>
                            <td><strong>{{ number_format($row['rata_rata'], 2) }}</strong></td>
                            <td>{{ $row['kkm'] }}</td>
                            <td>
                                <span class="badge {{ $row['keterangan'] === 'Tuntas' ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $row['keterangan'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($selectedMengajar && $row['siswa'])
                                    <a href="{{ route('guru.nilai.siswa', ['mengajar' => $selectedMengajar->id, 'siswa' => $row['siswa']->id]) }}" class="btn btn-xs btn-outline-primary" title="Input / Edit Nilai Siswa">
                                        <i class="bx bx-edit-alt"></i> Nilai
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">Data laporan nilai belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$paginatedLaporanNilai" align="end" />
        </div>
    </div>
@endsection
