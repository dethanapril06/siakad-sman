@extends('layouts.guru')

@section('title', 'Isi Absensi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pembelajaran / Absensi /</span> Isi</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.pertemuan.show', $pertemuan) }}" class="btn btn-outline-primary">
                <i class="bx bx-show me-1"></i>
                Detail Pertemuan
            </a>
            <a href="{{ route('guru.absensi.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if ($errors->has('absensi'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('absensi') }}</div>
    @endif

    <div class="card mb-4">
        <h5 class="card-header">Informasi Pertemuan</h5>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Pertemuan</small>
                    <span class="fw-semibold">Ke-{{ $pertemuan->pertemuan_ke }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal</small>
                    <span class="fw-semibold">{{ $pertemuan->tanggal->format('d M Y') }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Mata Pelajaran</small>
                    <span class="fw-semibold">{{ $pertemuan->mengajar?->mataPelajaran?->nama ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Kelas</small>
                    <span class="fw-semibold">{{ $pertemuan->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('guru.absensi.update', $pertemuan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="mb-0">Daftar Siswa</h5>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>
                    Simpan Absensi
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 120px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 180px;">Status Kehadiran</th>
                            <th style="width: 150px;">Terlambat?</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($pertemuan->mengajar?->kelasAkademik?->anggotaKelas ?? [] as $index => $anggota)
                            @php
                                $siswa = $anggota->siswa;
                                $absensi = $siswa ? $absensiExisting->get($siswa->id) : null;
                                $rawStatus = old("absensi.{$index}.status", $absensi?->status ?? 'hadir');
                                $isTerlambat = $rawStatus === 'terlambat' || old("absensi.{$index}.is_terlambat") === '1';
                                $displayStatus = $rawStatus === 'terlambat' ? 'hadir' : $rawStatus;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $siswa?->nis ?? '-' }}</strong>
                                    <input type="hidden" name="absensi[{{ $index }}][siswa_id]" value="{{ $siswa?->id }}">
                                </td>
                                <td>{{ $siswa?->nama ?? '-' }}</td>
                                <td>
                                    <select
                                        name="absensi[{{ $index }}][status]"
                                        class="form-select status-select @error('absensi.' . $index . '.status') is-invalid @enderror"
                                        data-row="{{ $index }}"
                                    >
                                        <option value="hadir" @selected($displayStatus === 'hadir')>Hadir</option>
                                        <option value="sakit" @selected($displayStatus === 'sakit')>Sakit</option>
                                        <option value="izin" @selected($displayStatus === 'izin')>Izin</option>
                                        <option value="alpa" @selected($displayStatus === 'alpa')>Alpa</option>
                                    </select>
                                    @error('absensi.' . $index . '.status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <select
                                        name="absensi[{{ $index }}][is_terlambat]"
                                        id="terlambat_{{ $index }}"
                                        class="form-select terlambat-select"
                                        @disabled($displayStatus !== 'hadir')
                                    >
                                        <option value="0" @selected(! $isTerlambat)>Tidak</option>
                                        <option value="1" @selected($isTerlambat)>Ya (Terlambat)</option>
                                    </select>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="absensi[{{ $index }}][keterangan]"
                                        class="form-control @error('absensi.' . $index . '.keterangan') is-invalid @enderror"
                                        value="{{ old("absensi.{$index}.keterangan", $absensi?->keterangan) }}"
                                        placeholder="Catatan / alasan (opsional)"
                                    />
                                    @error('absensi.' . $index . '.keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Anggota kelas belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelects = document.querySelectorAll('.status-select');

        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                const rowIndex = this.getAttribute('data-row');
                const terlambatSelect = document.getElementById('terlambat_' + rowIndex);

                if (terlambatSelect) {
                    if (this.value === 'hadir') {
                        terlambatSelect.disabled = false;
                    } else {
                        terlambatSelect.value = '0';
                        terlambatSelect.disabled = true;
                    }
                }
            });
        });
    });
    </script>
@endsection
