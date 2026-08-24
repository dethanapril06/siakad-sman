<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Nilai - {{ $semester?->tahunAkademik?->nama }}</title>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/boxicons.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Public Sans', Arial, sans-serif; }
        body { background-color: #f5f5f9; margin: 0; padding: 20px; color: #333; }
        .no-print-bar {
            max-width: 1000px; margin: 0 auto 20px auto; display: flex; justify-content: space-between;
            align-items: center; background: #fff; padding: 12px 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .btn-print { background: #696cff; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back { background: #e7e7ff; color: #696cff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; }
        .report-page { max-width: 1000px; margin: 0 auto; background: #fff; padding: 35px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-radius: 4px; }
        .kop-surat { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; text-align: center; }
        .kop-surat h4 { margin: 0; font-size: 12pt; font-weight: 600; text-transform: uppercase; }
        .kop-surat h2 { margin: 3px 0; font-size: 15pt; font-weight: 700; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; font-size: 8.5pt; color: #444; }
        .report-title { text-align: center; margin-bottom: 15px; }
        .report-title h3 { margin: 0; font-size: 13pt; font-weight: 700; text-transform: uppercase; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 8.5pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; text-align: center; }
        .data-table th { background-color: #f0f0f0; font-weight: 700; }
        .text-start { text-align: left !important; }
        .fw-bold { font-weight: 700; }
        .ttd-table { width: 100%; margin-top: 25px; font-size: 9pt; border-collapse: collapse; }
        .ttd-table td { text-align: center; vertical-align: top; width: 50%; }
        .ttd-space { height: 60px; }
        @media print {
            body { background: #fff; padding: 0; color: #000; }
            .no-print-bar { display: none !important; }
            .report-page { box-shadow: none; padding: 0; max-width: 100%; margin: 0; }
            .data-table th { background-color: #eee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
@php
    $backRoute = auth()->user()?->isKepalaSekolah()
        ? route('kepala-sekolah.laporan.nilai.index')
        : route('pegawai-tu.laporan.nilai.index');
@endphp

    <div class="no-print-bar">
        <div>
            <strong>Laporan Rekapitulasi Nilai Akademik</strong>
            <div style="font-size: 8.5pt; color: #777;">Format Siap Cetak</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ $backRoute }}" onclick="handleBack(event, '{{ $backRoute }}')" class="btn-back">Kembali</a>
            <button onclick="window.print()" class="btn-print">
                <i class="bx bx-printer"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="report-page">
        <div class="kop-surat">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 5px;">
                @if ($sekolahSetting->logo)
                    <img src="{{ asset('storage/' . $sekolahSetting->logo) }}" alt="Logo" style="height: 60px; object-fit: contain;">
                @endif
                <div>
                    <h4>{{ $sekolahSetting->nama_instansi }}</h4>
                    <h4>{{ $sekolahSetting->nama_dinas }}</h4>
                    <h2>{{ $sekolahSetting->nama_sekolah }}</h2>
                </div>
            </div>
            <p>{{ $sekolahSetting->alamat }} | Telepon: {{ $sekolahSetting->telepon ?? '-' }} | Website: {{ $sekolahSetting->website ?? '-' }}</p>
        </div>

        <div class="report-title">
            <h3>LAPORAN HASIL PENILAIAN AKADEMIK</h3>
            <p style="margin: 3px 0; font-size: 9.5pt;">Tahun Pelajaran: {{ $semester?->tahunAkademik?->nama }} - Semester {{ ucfirst($semester?->nama ?? '') }}</p>
        </div>

        @forelse ($laporanNilai as $laporan)
            <div style="margin-bottom: 20px;">
                <div style="font-size: 10pt; font-weight: 700; margin-bottom: 6px; display: flex; justify-content: space-between;">
                    <span>Kelas: {{ $laporan['kelas_akademik']->nama_lengkap }} ({{ $laporan['kelas_akademik']->kelas?->jurusan?->nama }})</span>
                    <span>Wali Kelas: {{ $laporan['kelas_akademik']->guru?->nama ?? '-' }}</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th style="width: 75px;">NISN</th>
                                <th class="text-start" style="min-width: 150px;">Nama Siswa</th>
                                @foreach ($laporan['mata_pelajarans'] as $mapel)
                                    <th title="{{ $mapel->nama }}">{{ $mapel->kode }}</th>
                                @endforeach
                                <th style="width: 55px;">Rata2</th>
                                <th style="width: 70px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan['raport_nilai'] as $item)
                                <tr>
                                    <td>{{ $item['no'] }}</td>
                                    <td>{{ $item['siswa']->nisn ?? '-' }}</td>
                                    <td class="text-start fw-bold">{{ $item['siswa']->nama }}</td>
                                    @foreach ($laporan['mata_pelajarans'] as $mapel)
                                        @php
                                            $val = $item['nilai_mapel'][$mapel->id]['nilai_akhir'] ?? null;
                                        @endphp
                                        <td>{{ $val ?? '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold">{{ $item['rata_rata'] ?? '-' }}</td>
                                    <td>
                                        <span class="{{ $item['keterangan'] === 'Tuntas' ? 'text-success' : 'text-danger' }} fw-bold">
                                             {{ $item['keterangan'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 5 + $laporan['mata_pelajarans']->count() }}">Tidak ada data nilai di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px;">Tidak ada data laporan nilai yang sesuai filter.</div>
        @endforelse

        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>Kepala Sekolah
                    <div class="ttd-space"></div>
                    <strong>{{ $sekolahSetting->kepala_sekolah_nama }}</strong><br>
                    <small>NIP. {{ $sekolahSetting->kepala_sekolah_nip ?? '........................' }}</small>
                </td>
                <td>
                    {{ $sekolahSetting->kepala_sekolah_ttd_lokasi ?? 'Kupang' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Petugas / Bagian Akademik
                    <div class="ttd-space"></div>
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <small>{{ $sekolahSetting->nama_sekolah }}</small>
                </td>
            </tr>
        </table>
    </div>
    <script>
        function handleBack(event, fallbackUrl) {
            if (window.opener || window.history.length <= 1) {
                window.close();
            }
            if (window.history.length > 1 && document.referrer && document.referrer.includes(window.location.host)) {
                event.preventDefault();
                window.history.back();
                return;
            }
        }
    </script>
</body>
</html>
