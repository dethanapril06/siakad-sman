@extends('layouts.guru')

@section('title', 'Penilaian Siswa - ' . $siswa->nama)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Penilaian /</span> {{ $siswa->nama }}
            </h4>
            <span class="text-muted">
                {{ $mengajar->mataPelajaran?->nama }} - {{ $mengajar->kelasAkademik?->nama_lengkap }} ({{ $mengajar->semester?->nama_lengkap }})
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.laporan-nilai.index', ['mengajar_id' => $mengajar->id]) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Rekap
            </a>
            <a href="{{ route('guru.mengajar.show', $mengajar) }}" class="btn btn-outline-primary">
                <i class="bx bx-book-open me-1"></i> Detail Mengajar
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ringkasan Siswa & Rata-rata Nilai --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary fs-4">
                                {{ strtoupper(substr($siswa->nama, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $siswa->nama }}</h5>
                            <span class="text-muted small">NIS: {{ $siswa->nis ?? '-' }} | NISN: {{ $siswa->nisn ?? '-' }}</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Kelas</span>
                        <span class="fw-semibold">{{ $mengajar->kelasAkademik?->nama_lengkap }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mata Pelajaran</span>
                        <span class="fw-semibold">{{ $mengajar->mataPelajaran?->nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Semester</span>
                        <span class="fw-semibold">{{ $mengajar->semester?->nama_lengkap }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Rata-rata Nilai & Predikat (Real-time)</h5>
                    <span class="badge bg-label-primary">KKM 75</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        @foreach ($jenisNilais as $jenis)
                            <div class="col-sm-4 col-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">{{ $jenis->nama }}</small>
                                    <small class="badge bg-label-secondary mb-1">Bobot {{ $jenis->bobot }}%</small>
                                    <h4 class="mb-0 mt-1" id="avg-{{ $jenis->kode }}">-</h4>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-sm-4 col-12">
                            <div class="p-2 border rounded bg-primary text-white">
                                <small class="text-white-50 d-block">Nilai Akhir Rapor</small>
                                <small class="badge bg-white text-primary mb-1" id="badge-predikat">Predikat: -</small>
                                <h3 class="mb-0 mt-1 text-white" id="val-nilai-akhir">-</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Input Nilai Per Kategori --}}
    <form action="{{ route('guru.nilai.siswa.update', ['mengajar' => $mengajar->id, 'siswa' => $siswa->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Penilaian Mata Pelajaran</h5>
                <small class="text-muted">Isi atau ubah nilai siswa pada kolom di bawah ini</small>
            </div>

            @if ($penilaians->isEmpty())
                <div class="card-body text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="bx bx-folder-open fs-1"></i>
                    </div>
                    <h5>Belum Ada Agenda Penilaian</h5>
                    <p class="text-muted">Buat agenda penilaian (Harian, Tugas, Keterampilan, UTS, UAS) terlebih dahulu pada menu Penilaian.</p>
                    <a href="{{ route('guru.penilaian.create', ['mengajar_id' => $mengajar->id]) }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Buat Penilaian
                    </a>
                </div>
            @else
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 140px;">Kategori</th>
                                <th>Nama Penilaian / Materi</th>
                                <th style="width: 130px;">Tanggal</th>
                                <th style="width: 160px;">Nilai (0 - 100)</th>
                                <th>Catatan Guru</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($penilaians as $index => $penilaian)
                                @php
                                    $existing = $nilais->get($penilaian->id);
                                    $currentVal = old('nilai.' . $penilaian->id, $existing?->nilai);
                                    $currentCatatan = old('catatan.' . $penilaian->id, $existing?->catatan);
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            {{ $penilaian->jenisNilai?->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $penilaian->nama }}</strong>
                                        @if ($penilaian->keterangan)
                                            <div class="small text-muted text-truncate" style="max-width: 300px;">
                                                {{ $penilaian->keterangan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $penilaian->tanggal ? \Carbon\Carbon::parse($penilaian->tanggal)->isoFormat('D MMM Y') : '-' }}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-merge" style="max-width: 130px;">
                                            <input
                                                type="number"
                                                name="nilai[{{ $penilaian->id }}]"
                                                class="form-control grade-input @error('nilai.' . $penilaian->id) is-invalid @enderror"
                                                value="{{ $currentVal !== null ? (float) $currentVal : '' }}"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                placeholder="0-100"
                                                data-kode="{{ $penilaian->jenisNilai?->kode }}"
                                            />
                                        </div>
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="catatan[{{ $penilaian->id }}]"
                                            class="form-control"
                                            value="{{ $currentCatatan }}"
                                            placeholder="Catatan untuk siswa (opsional)"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="bx bx-info-circle me-1"></i> Nilai akan otomatis masuk ke laporan nilai guru dan rapor siswa.
                    </span>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-save me-1"></i> Simpan Semua Nilai
                    </button>
                </div>
            @endif
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const bobotConfig = @json($bobotMap);
        const inputs = document.querySelectorAll('.grade-input');

        function calculateAverages() {
            const grouped = {};

            inputs.forEach(input => {
                const kode = input.getAttribute('data-kode');
                const val = parseFloat(input.value);

                if (!grouped[kode]) {
                    grouped[kode] = [];
                }

                if (!isNaN(val)) {
                    grouped[kode].push(val);
                }
            });

            let totalBobot = 0;
            let totalWeightedScore = 0;

            for (const kode in bobotConfig) {
                const badgeAvg = document.getElementById('avg-' + kode);
                const values = grouped[kode] || [];
                const bobot = bobotConfig[kode] || 0;

                if (values.length > 0) {
                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    if (badgeAvg) {
                        badgeAvg.textContent = avg.toFixed(2);
                    }
                    totalWeightedScore += avg * bobot;
                    totalBobot += bobot;
                } else {
                    if (badgeAvg) {
                        badgeAvg.textContent = '-';
                    }
                }
            }

            const valNilaiAkhir = document.getElementById('val-nilai-akhir');
            const badgePredikat = document.getElementById('badge-predikat');

            if (totalBobot > 0) {
                const finalScore = totalWeightedScore / totalBobot;
                valNilaiAkhir.textContent = finalScore.toFixed(2);

                let predikat = 'D';
                if (finalScore >= 90) predikat = 'A';
                else if (finalScore >= 80) predikat = 'B';
                else if (finalScore >= 75) predikat = 'C';

                badgePredikat.textContent = 'Predikat: ' + predikat;
            } else {
                valNilaiAkhir.textContent = '-';
                badgePredikat.textContent = 'Predikat: -';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateAverages);
        });

        calculateAverages();
    });
    </script>
@endsection
