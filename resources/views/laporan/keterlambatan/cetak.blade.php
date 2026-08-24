<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keterlambatan Siswa - {{ $semester?->tahunAkademik?->nama }}</title>
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
        ? route('kepala-sekolah.laporan.keterlambatan.index')
        : route('pegawai-tu.laporan.keterlambatan.index');
@endphp

    <div class="no-print-bar">
        <div>
            <strong>Laporan Keterlambatan Siswa</strong>
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
            <h4>PEMERINTAH PROVINSI DAERAH</h4>
            <h4>DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
            <h2>SMA NEGERI SIADKAD</h2>
            <p>Jl. Pendidikan No. 123, Telepon: (021) 1234567 | Website: www.sman-siakad.sch.id</p>
        </div>

        <div class="report-title">
            <h3>LAPORAN KEDISIPLINAN & KETERLAMBATAN SISWA</h3>
            <p style="margin: 3px 0; font-size: 9.5pt;">Tahun Pelajaran: {{ $semester?->tahunAkademik?->nama }} - Semester {{ ucfirst($semester?->nama ?? '') }}</p>
            <p style="margin: 2px 0; font-size: 8.5pt; color: #555;">Total Kejadian Keterlambatan: <strong>{{ $keterlambatans->count() }} Kali</strong></p>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 70px;">Tanggal</th>
                    <th class="text-start" style="min-width: 140px;">Nama Siswa</th>
                    <th style="width: 90px;">Kelas</th>
                    <th class="text-start">Mata Pelajaran</th>
                    <th class="text-start">Guru Pengampu</th>
                    <th>Catatan / Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keterlambatans as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $item->pertemuan?->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-start fw-bold">{{ $item->siswa?->nama ?? '-' }}</td>
                        <td>{{ $item->pertemuan?->mengajar?->kelasAkademik?->nama_lengkap ?? '-' }}</td>
                        <td class="text-start">{{ $item->pertemuan?->mengajar?->mataPelajaran?->nama ?? '-' }}</td>
                        <td class="text-start">{{ $item->pertemuan?->mengajar?->guru?->nama ?? '-' }}</td>
                        <td class="text-start">{{ $item->catatan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">Tidak ada data keterlambatan yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>Kepala Sekolah
                    <div class="ttd-space"></div>
                    <strong>{{ $kepalaSekolah?->nama ?? 'Nama Kepala Sekolah, M.Pd' }}</strong><br>
                    <small>NIP. {{ $kepalaSekolah?->nip ?? '........................' }}</small>
                </td>
                <td>
                    Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Koordinator Guru Piket / BK
                    <div class="ttd-space"></div>
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <small>SIAKAD SMAN</small>
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
