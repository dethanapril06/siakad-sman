@extends('layouts.guru')

@section('title', 'Input Nilai')

@section('content')
    @php
        $anggotaKelas = $penilaian->mengajar?->kelasAkademik?->anggotaKelas ?? collect();
        $jumlahSiswa = $anggotaKelas->count();
        $jumlahDinilai = $nilaiExisting->count();
        $nilaiTersimpan = $nilaiExisting->pluck('nilai')->map(fn ($nilai) => (float) $nilai);
        $rataRata = $nilaiTersimpan->isNotEmpty() ? $nilaiTersimpan->avg() : null;
        $nilaiTertinggi = $nilaiTersimpan->isNotEmpty() ? $nilaiTersimpan->max() : null;
        $nilaiTerendah = $nilaiTersimpan->isNotEmpty() ? $nilaiTersimpan->min() : null;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran / Penilaian /</span> Input Nilai</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.penilaian.show', $penilaian) }}" class="btn btn-outline-primary">
                <i class="bx bx-show me-1"></i>
                Detail Penilaian
            </a>
            <a href="{{ route('guru.penilaian.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if ($errors->has('nilai'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('nilai') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Jumlah Siswa</small>
                    <h4 class="mb-0">{{ $jumlahSiswa }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Sudah Dinilai</small>
                    <h4 class="mb-0">{{ $jumlahDinilai }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Rata-rata</small>
                    <h4 class="mb-0">{{ $rataRata !== null ? number_format($rataRata, 2) : '-' }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Rentang Nilai</small>
                    <h4 class="mb-0">
                        {{ $nilaiTerendah !== null ? number_format($nilaiTerendah, 2) : '-' }}
                        -
                        {{ $nilaiTertinggi !== null ? number_format($nilaiTertinggi, 2) : '-' }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Informasi Penilaian</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Judul</small>
                    <span class="fw-semibold">{{ $penilaian->judul }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Jenis</small>
                    <span class="fw-semibold">{{ $penilaian->jenisNilai?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Mata Pelajaran</small>
                    <span class="fw-semibold">{{ $penilaian->mengajar?->mataPelajaran?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $penilaian->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Semester</small>
                    <span class="fw-semibold">
                        {{ ucfirst($penilaian->mengajar?->semester?->nama ?? '-') }}
                        - {{ $penilaian->mengajar?->semester?->tahunAkademik?->nama ?? '-' }}
                    </span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal</small>
                    <span class="fw-semibold">{{ $penilaian->tanggal->format('d M Y') }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge {{ $jumlahSiswa > 0 && $jumlahDinilai >= $jumlahSiswa ? 'bg-label-success' : 'bg-label-warning' }}">
                        {{ $jumlahDinilai }}/{{ $jumlahSiswa }} siswa
                    </span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('guru.nilai.update', $penilaian) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="mb-0">Daftar Siswa</h5>
                <button type="submit" class="btn btn-primary" @disabled($jumlahSiswa === 0)>
                    <i class="bx bx-save me-1"></i>
                    Simpan Nilai
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Nilai</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($anggotaKelas as $index => $anggota)
                            @php
                                $siswa = $anggota->siswa;
                                $nilai = $siswa ? $nilaiExisting->get($siswa->id) : null;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $siswa?->nis ?? '-' }}</strong>
                                    <input type="hidden" name="nilai[{{ $index }}][siswa_id]" value="{{ $siswa?->id }}">
                                </td>
                                <td>{{ $siswa?->nama ?? '-' }}</td>
                                <td>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        name="nilai[{{ $index }}][nilai]"
                                        class="form-control @error('nilai.' . $index . '.nilai') is-invalid @enderror"
                                        value="{{ old("nilai.{$index}.nilai", $nilai?->nilai) }}"
                                        placeholder="0-100"
                                    />
                                    @error('nilai.' . $index . '.siswa_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('nilai.' . $index . '.nilai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="nilai[{{ $index }}][catatan]"
                                        class="form-control @error('nilai.' . $index . '.catatan') is-invalid @enderror"
                                        value="{{ old("nilai.{$index}.catatan", $nilai?->catatan) }}"
                                        placeholder="Opsional"
                                    />
                                    @error('nilai.' . $index . '.catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Anggota kelas belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
@endsection
