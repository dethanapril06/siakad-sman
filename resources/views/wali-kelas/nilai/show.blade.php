@extends('layouts.guru')

@section('title', 'Detail Raport Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Wali Kelas / Raport /</span> Detail</h4>
        <a href="{{ route('wali-kelas.nilai.index', ['semester_id' => $semesterId]) }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Siswa</small>
                    <h4 class="mb-0">{{ $siswa->nama }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">NIS</small>
                    <span class="fw-semibold">{{ $siswa->nis }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $kelasWali->nama_lengkap }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ $semesters->firstWhere('id', $semesterId)?->nama ? ucfirst($semesters->firstWhere('id', $semesterId)->nama) : '-' }}
                        - {{ $semesters->firstWhere('id', $semesterId)?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <small class="text-muted d-block mb-1">Bobot nilai akhir</small>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-label-secondary">Harian {{ $bobot['NH'] }}%</span>
                <span class="badge bg-label-secondary">Tugas {{ $bobot['TUGAS'] }}%</span>
                <span class="badge bg-label-secondary">UTS {{ $bobot['UTS'] }}%</span>
                <span class="badge bg-label-secondary">UAS {{ $bobot['UAS'] }}%</span>
                <span class="badge bg-label-primary">KKM {{ $kkm }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Nilai Akhir Mata Pelajaran</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Nilai Harian</th>
                        <th>Nilai Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Nilai Akhir</th>
                        <th>KKM</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($raportSiswa as $row)
                        <tr>
                            <td><strong>{{ $row['mata_pelajaran']->nama }}</strong></td>
                            <td>{{ $row['rekap']['nilai_harian'] !== null ? number_format($row['rekap']['nilai_harian'], 2) : '-' }}</td>
                            <td>{{ $row['rekap']['nilai_tugas'] !== null ? number_format($row['rekap']['nilai_tugas'], 2) : '-' }}</td>
                            <td>{{ $row['rekap']['nilai_uts'] !== null ? number_format($row['rekap']['nilai_uts'], 2) : '-' }}</td>
                            <td>{{ $row['rekap']['nilai_uas'] !== null ? number_format($row['rekap']['nilai_uas'], 2) : '-' }}</td>
                            <td><strong>{{ $row['rekap']['nilai_akhir'] !== null ? number_format($row['rekap']['nilai_akhir'], 2) : '-' }}</strong></td>
                            <td>{{ $row['rekap']['kkm'] }}</td>
                            <td>
                                <span class="badge {{ $row['rekap']['keterangan'] === 'Tuntas' ? 'bg-label-success' : ($row['rekap']['keterangan'] === 'Belum Tuntas' ? 'bg-label-danger' : 'bg-label-secondary') }}">
                                    {{ $row['rekap']['keterangan'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Data raport siswa belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
