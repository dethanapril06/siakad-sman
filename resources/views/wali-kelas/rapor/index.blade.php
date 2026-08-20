@extends('layouts.guru')

@section('title', 'E-Rapor & Catatan Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Wali Kelas /</span> E-Rapor & Catatan Siswa</h4>
            <span class="text-muted">Kelas: <strong>{{ $kelasWali->nama_lengkap }}</strong> ({{ $kelasWali->kelas?->jurusan?->nama }})</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Info Card Status Akses Rapor dari TU --}}
    <div class="card mb-4 border-start border-4 {{ $semesterAktif?->is_rapor_open ? 'border-success' : 'border-warning' }}">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded {{ $semesterAktif?->is_rapor_open ? 'bg-label-success' : 'bg-label-warning' }}">
                            <i class="bx {{ $semesterAktif?->is_rapor_open ? 'bx-lock-open-alt' : 'bx-lock-alt' }} fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark fw-bold">
                            Status Publikasi Cetak Rapor: 
                            <span class="badge {{ $semesterAktif?->is_rapor_open ? 'bg-success' : 'bg-warning' }}">
                                {{ $semesterAktif?->is_rapor_open ? 'DIBUKA OLEH TU' : 'BELUM DIBUKA OLEH TU' }}
                            </span>
                        </h6>
                        <small class="text-muted">
                            @if ($semesterAktif?->is_rapor_open)
                                Akses cetak rapor untuk siswa & wali murid telah diaktifkan oleh Tata Usaha. Tanggal Rapor: <strong>{{ $semesterAktif->tanggal_rapor ? $semesterAktif->tanggal_rapor->format('d M Y') : 'Sesuai jadwal' }}</strong>.
                            @else
                                Bagian Tata Usaha belum membuka akses publikasi rapor resmi. Anda tetap dapat menginput catatan wali kelas.
                            @endif
                        </small>
                    </div>
                </div>

                {{-- Filter Semester --}}
                <form action="{{ route('wali-kelas.rapor.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" @selected((string) $semesterAktif?->id === (string) $sem->id)>
                                {{ ucfirst($sem->nama) }} - {{ $sem->tahunAkademik?->nama }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Siswa & Aksi Rapor --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Siswa & Kelengkapan Rapor</h5>
            <small class="text-muted">Total: {{ $anggotaKelas->count() }} Siswa</small>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NISN & Nama Siswa</th>
                        <th>Rata-rata Nilai</th>
                        <th>Peringkat</th>
                        <th>Kehadiran (H/S/I/A)</th>
                        <th>Catatan Wali Kelas</th>
                        <th class="text-end">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($anggotaKelas as $index => $anggota)
                        @php
                            $siswa = $anggota->siswa;
                            $stats = $siswaStats[$siswa->id] ?? null;
                            $catatanWali = $siswa->catatanWaliKelas->first();
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $siswa->nama }}</strong>
                                <div class="small text-muted">NISN: {{ $siswa->nisn ?? '-' }} | NIS: {{ $siswa->nis ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $stats && $stats['rata_rata'] >= 75 ? 'bg-label-success' : 'bg-label-warning' }} fs-6">
                                    {{ $stats['rata_rata'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($stats && $stats['rank'])
                                    <span class="badge bg-label-primary">Rank #{{ $stats['rank'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($stats)
                                    <small class="fw-semibold">
                                        <span class="text-success">{{ $stats['presensi']['hadir'] }} H</span> /
                                        <span class="text-primary">{{ $stats['presensi']['sakit'] }} S</span> /
                                        <span class="text-info">{{ $stats['presensi']['izin'] }} I</span> /
                                        <span class="text-danger">{{ $stats['presensi']['alpa'] }} A</span>
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($catatanWali && $catatanWali->catatan)
                                    <span class="text-truncate d-inline-block text-success fw-semibold" style="max-width: 180px;" title="{{ $catatanWali->catatan }}">
                                        <i class="bx bx-check-circle me-1"></i> Terisi
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Belum Diisi</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    {{-- Button Modal Catatan --}}
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCatatan{{ $siswa->id }}" title="Input Catatan Wali">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    {{-- Button Cetak Rapor --}}
                                    <a href="{{ route('wali-kelas.rapor.cetak', ['siswa' => $siswa->id, 'semester_id' => $semesterAktif?->id]) }}" target="_blank" class="btn btn-sm btn-primary" title="Cetak Lembar Rapor">
                                        <i class="bx bx-printer me-1"></i> Rapor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Data siswa belum tersedia di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL INPUT CATATAN WALI KELAS --}}
    @foreach ($anggotaKelas as $anggota)
        @php
            $siswa = $anggota->siswa;
            $catatanWali = $siswa->catatanWaliKelas->first();
        @endphp
        <div class="modal fade" id="modalCatatan{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('wali-kelas.rapor.catatan', $siswa) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="semester_id" value="{{ $semesterAktif?->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title">Catatan Wali Kelas: {{ $siswa->nama }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="catatan{{ $siswa->id }}">Catatan & Motivasi Perkembangan Belajar</label>
                                <textarea class="form-control" id="catatan{{ $siswa->id }}" name="catatan" rows="4" placeholder="Tuliskan catatan perkembangan belajar siswa...">{{ old('catatan', $catatanWali?->catatan) }}</textarea>
                                <small class="text-muted">Catatan ini akan dicetak pada lembar rapor siswa.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="status_kenaikan{{ $siswa->id }}">Keputusan / Kenaikan Kelas (Khusus Semester Genap)</label>
                                <input type="text" class="form-control" id="status_kenaikan{{ $siswa->id }}" name="status_kenaikan" value="{{ old('status_kenaikan', $catatanWali?->status_kenaikan) }}" placeholder="Contoh: Naik ke Kelas XI / Lulus">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
