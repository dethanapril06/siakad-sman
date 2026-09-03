@extends('layouts.siswa')

@section('title', 'Nilai')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Akademik /</span> Nilai</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas Aktif</small>
                    <h4 class="mb-0">{{ $kelasAkademik?->nama_lengkap ?? '-' }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Jumlah Penilaian</small>
                    <h4 class="mb-0">{{ $ringkasan['jumlah_penilaian'] }}</h4>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Rata-rata</small>
                    <h4 class="mb-0">{{ number_format((float) $ringkasan['rata_rata'], 2) }}</h4>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Tertinggi</small>
                    <h4 class="mb-0">{{ $ringkasan['nilai_tertinggi'] !== null ? number_format((float) $ringkasan['nilai_tertinggi'], 2) : '-' }}</h4>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Terendah</small>
                    <h4 class="mb-0">{{ $ringkasan['nilai_terendah'] !== null ? number_format((float) $ringkasan['nilai_terendah'], 2) : '-' }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Nilai</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.nilai.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
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
                        <label class="form-label" for="jenis_nilai_id">Jenis Nilai</label>
                        <select name="jenis_nilai_id" id="jenis_nilai_id" class="form-select">
                            <option value="">Semua jenis</option>
                            @foreach ($jenisNilais as $jenisNilai)
                                <option value="{{ $jenisNilai->id }}" @selected((string) $jenisNilaiId === (string) $jenisNilai->id)>
                                    {{ $jenisNilai->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @forelse ($paginatedNilaiPerMataPelajaran as $nilaiMapel)
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">{{ $nilaiMapel['mata_pelajaran']?->nama ?? '-' }}</h5>
                    <small class="text-muted">Guru: {{ $nilaiMapel['guru']?->nama ?? '-' }}</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-label-primary">Rata-rata {{ number_format((float) $nilaiMapel['rata_rata'], 2) }}</span>
                    <span class="badge bg-label-success">Tertinggi {{ $nilaiMapel['nilai_tertinggi'] !== null ? number_format((float) $nilaiMapel['nilai_tertinggi'], 2) : '-' }}</span>
                    <span class="badge bg-label-warning">Terendah {{ $nilaiMapel['nilai_terendah'] !== null ? number_format((float) $nilaiMapel['nilai_terendah'], 2) : '-' }}</span>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Penilaian</th>
                            <th>Nilai</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($nilaiMapel['nilais'] as $nilai)
                            <tr>
                                <td>{{ $nilai->penilaian?->tanggal?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $nilai->penilaian?->jenisNilai?->nama ?? '-' }}</td>
                                <td><strong>{{ $nilai->penilaian?->judul ?? '-' }}</strong></td>
                                <td>{{ number_format((float) $nilai->nilai, 2) }}</td>
                                <td>{{ $nilai->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-4">
                {{ $kelasAkademik ? 'Nilai belum tersedia.' : 'Kelas aktif belum tersedia.' }}
            </div>
        </div>
    @endforelse

    <div class="d-flex justify-content-end my-4">
        <x-pagination :paginator="$paginatedNilaiPerMataPelajaran" align="end" />
    </div>
@endsection
