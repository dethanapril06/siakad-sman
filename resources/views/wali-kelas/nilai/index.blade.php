@extends('layouts.guru')

@section('title', 'Raport Kelas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas /</span> Raport Kelas</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kelas Wali</small>
                    <h4 class="mb-0">{{ $kelasWali->nama_lengkap }}</h4>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ $semesters->firstWhere('id', $semesterId)?->nama ? ucfirst($semesters->firstWhere('id', $semesterId)->nama) : '-' }}
                        - {{ $semesters->firstWhere('id', $semesterId)?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Jumlah Siswa</small>
                    <h4 class="mb-0">{{ $raportNilai->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0">Filter Raport</h5>
            <span class="badge bg-label-primary">KKM {{ $kkm }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('wali-kelas.nilai.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label class="form-label" for="semester_id">Semester</label>
                        <select name="semester_id" id="semester_id" class="form-select">
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>
                                    {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
                                </option>
                            @endforeach
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

    <div class="card">
        <h5 class="card-header">Nilai Akhir Per Mata Pelajaran</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        @foreach ($mataPelajarans as $mataPelajaran)
                            <th>{{ $mataPelajaran->nama }}</th>
                        @endforeach
                        <th>Rata-rata</th>
                        <th>Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($paginatedRaportNilai as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td><strong>{{ $row['siswa']?->nis ?? '-' }}</strong></td>
                            <td>{{ $row['siswa']?->nama ?? '-' }}</td>
                            @foreach ($mataPelajarans as $mataPelajaran)
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
                            <td class="text-end">
                                @if ($row['siswa'])
                                    <a href="{{ route('wali-kelas.nilai.show', ['siswa' => $row['siswa'], 'semester_id' => $semesterId]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show me-1"></i>
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $mataPelajarans->count() }}" class="text-center py-4">Data raport belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$paginatedRaportNilai" align="end" />
        </div>
    </div>
@endsection
