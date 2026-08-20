@extends('layouts.siswa')

@section('title', 'Rapor Digital Saya')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Siswa /</span> Rapor Digital Saya</h4>
            <span class="text-muted">Pantau lembar evaluasi dan capaian hasil belajar semester</span>
        </div>

        {{-- Filter Semester --}}
        <form action="{{ route('siswa.rapor.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="semester_id" class="form-select" onchange="this.form.submit()">
                @foreach ($semesters as $sem)
                    <option value="{{ $sem->id }}" @selected((string) $semesterAktif?->id === (string) $sem->id)>
                        {{ ucfirst($sem->nama) }} - {{ $sem->tahunAkademik?->nama }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if (! $isRaporOpen)
        {{-- Kondisi Rapor Belum Dibuka Oleh TU --}}
        <div class="card text-center py-5">
            <div class="card-body">
                <div class="avatar avatar-xl bg-label-warning mx-auto mb-4" style="width: 72px; height: 72px;">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="bx bx-lock-alt fs-1"></i>
                    </span>
                </div>
                <h4 class="card-title text-dark mb-2">Pencetakan Rapor Belum Dibuka</h4>
                <p class="text-muted max-w-md mx-auto mb-4" style="max-width: 540px;">
                    Akses publikasi lembar rapor resmi untuk <strong>Semester {{ $semesterAktif?->nama ? ucfirst($semesterAktif->nama) : '' }} ({{ $semesterAktif?->tahunAkademik?->nama }})</strong> saat ini masih dalam proses finalisasi nilai dan belum dibuka oleh Bagian Tata Usaha.
                </p>
                <div class="d-inline-flex align-items-center gap-2 alert alert-warning py-2 px-3 mb-0">
                    <i class="bx bx-info-circle"></i>
                    <span>Silakan cek secara berkala atau hubungi Wali Kelas Anda untuk informasi pengumuman pembagian rapor.</span>
                </div>
            </div>
        </div>
    @else
        {{-- Kondisi Rapor Sudah Dibuka Oleh TU --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-white text-primary fw-bold">E-Rapor Resmi</span>
                                <span class="badge bg-white bg-opacity-25 text-white">
                                    Semester {{ ucfirst($semesterAktif->nama) }} {{ $semesterAktif->tahunAkademik?->nama }}
                                </span>
                            </div>
                            <h3 class="card-title text-white mb-2">{{ $siswa->nama }}</h3>
                            <p class="mb-0 text-white text-opacity-75">
                                NISN: {{ $siswa->nisn ?? '-' }} | Kelas: {{ $raporData['kelasAkademik']?->nama_lengkap ?? '-' }} | Wali Kelas: {{ $raporData['kelasAkademik']?->guru?->nama ?? '-' }}
                            </p>
                        </div>
                        <div class="pt-4 mt-3 border-top border-white border-opacity-25 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <span class="text-white text-opacity-75 small">
                                <i class="bx bx-calendar me-1"></i> Tanggal Rapor: {{ $semesterAktif->tanggal_rapor ? $semesterAktif->tanggal_rapor->format('d M Y') : 'Sesuai agenda sekolah' }}
                            </span>
                            <a href="{{ route('siswa.rapor.cetak', ['semester_id' => $semesterAktif->id]) }}" target="_blank" class="btn btn-light text-primary fw-bold shadow-sm">
                                <i class="bx bx-printer me-1"></i> Cetak Lembar Rapor (PDF)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-around">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-muted d-block small">Rata-rata Nilai</span>
                                <h2 class="mb-0 fw-bold text-primary">{{ $raporData['rataRata'] ?? '-' }}</h2>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-award fs-3"></i></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                            <div>
                                <span class="text-muted d-block small">Peringkat Kelas</span>
                                <h4 class="mb-0 fw-bold text-dark">
                                    @if ($raporData['peringkat'])
                                        Ke-{{ $raporData['peringkat'] }} <small class="text-muted font-normal fs-6">dari {{ $raporData['totalSiswa'] }} siswa</small>
                                    @else
                                        -
                                    @endif
                                </h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-trophy fs-3"></i></span>
                            </div>
                        </div>

                        <div class="pt-2 border-top">
                            <span class="text-muted d-block small mb-1">Presensi Kehadiran:</span>
                            <div class="d-flex justify-content-between text-center">
                                <div>
                                    <small class="text-muted d-block">Hadir</small>
                                    <strong class="text-success">{{ $raporData['presensi']['hadir'] }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Sakit</small>
                                    <strong class="text-primary">{{ $raporData['presensi']['sakit'] }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Izin</small>
                                    <strong class="text-info">{{ $raporData['presensi']['izin'] }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Alpa</small>
                                    <strong class="text-danger">{{ $raporData['presensi']['alpa'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Capaian Nilai Rapor --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Rincian Capaian Mata Pelajaran</h5>
                <span class="badge bg-label-primary">Standar KKM: 75</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th class="text-center">KKM</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center">Predikat</th>
                            <th>Status Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($raporData['nilaiMapel'] as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['mata_pelajaran']->nama }}</strong>
                                    <div class="small text-muted">{{ $item['mata_pelajaran']->kode }}</div>
                                </td>
                                <td class="text-center">{{ $item['kkm'] }}</td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6 {{ $item['nilai_akhir'] && $item['nilai_akhir'] >= 75 ? 'text-success' : 'text-danger' }}">
                                        {{ $item['nilai_akhir'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item['predikat'] === 'A' ? 'bg-label-success' : ($item['predikat'] === 'B' ? 'bg-label-primary' : ($item['predikat'] === 'C' ? 'bg-label-warning' : 'bg-label-danger')) }}">
                                        {{ $item['predikat'] }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted d-block text-wrap" style="max-width: 380px;">
                                        {{ $item['capaian_kompetensi'] }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Data nilai belum tersedia pada semester ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Catatan Wali Kelas --}}
        @if ($raporData['catatanWali'])
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Catatan & Motivasi Wali Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded border">
                        <p class="mb-2 fst-italic text-dark">
                            "{{ $raporData['catatanWali']->catatan ?? 'Tingkatkan terus motivasi belajar dan kedisiplinan di sekolah.' }}"
                        </p>
                        @if ($raporData['catatanWali']->status_kenaikan)
                            <div class="mt-2 text-primary fw-bold">
                                <i class="bx bx-check-circle me-1"></i> Keputusan: {{ $raporData['catatanWali']->status_kenaikan }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection
