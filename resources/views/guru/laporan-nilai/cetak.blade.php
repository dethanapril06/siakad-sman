<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Nilai - {{ $selectedMengajar->mataPelajaran?->nama }} - {{ $selectedMengajar->kelasAkademik?->nama_lengkap }}</title>
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
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 9pt; }
        .meta-table td { padding: 3px 0; }
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
        $backRoute = route('guru.laporan-nilai.index', ['mengajar_id' => $selectedMengajar->id]);
        $sekolah = \App\Models\Sekolah::first();
    @endphp

    <div class="no-print-bar">
        <div>
            <strong>Laporan Nilai Mata Pelajaran</strong>
            <div style="font-size: 8.5pt; color: #777;">Format Siap Cetak</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ $backRoute }}" class="btn-back">Kembali</a>
            <button onclick="window.print()" class="btn-print">
                <i class="bx bx-printer"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="report-page">
        <div class="kop-surat">
            <h4>PEMERINTAH PROVINSI DAERAH</h4>
            <h4>DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
            <h2>{{ $sekolah?->nama ?? 'SMA NEGERI 1' }}</h2>
            <p>{{ $sekolah?->alamat ?? 'Alamat Sekolah' }} | Telp: {{ $sekolah?->no_telepon ?? '-' }} | Email: {{ $sekolah?->email ?? '-' }}</p>
        </div>

        <div class="report-title">
            <h3>REKAPITULASI NILAI AKADEMIK SISWA</h3>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 18%;"><strong>Mata Pelajaran</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $selectedMengajar->mataPelajaran?->nama }}</td>
                <td style="width: 18%;"><strong>Semester / T.A.</strong></td>
                <td style="width: 2%;">:</td>
                <td>{{ $selectedMengajar->semester?->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>:</td>
                <td>{{ $selectedMengajar->kelasAkademik?->nama_lengkap }}</td>
                <td><strong>Guru Pengampu</strong></td>
                <td>:</td>
                <td>{{ $guru->nama }}</td>
            </tr>
            <tr>
                <td><strong>KKM</strong></td>
                <td>:</td>
                <td>{{ $kkm }}</td>
                <td><strong>Bobot Nilai</strong></td>
                <td>:</td>
                <td>Harian ({{ $bobot['NH'] ?? 20 }}%), Tugas ({{ $bobot['TUGAS'] ?? 20 }}%), Keterampilan ({{ $bobot['KTR'] ?? 20 }}%), UTS ({{ $bobot['UTS'] ?? 20 }}%), UAS ({{ $bobot['UAS'] ?? 20 }}%)</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 90px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 65px;">Harian<br>({{ $bobot['NH'] ?? 20 }}%)</th>
                    <th style="width: 65px;">Tugas<br>({{ $bobot['TUGAS'] ?? 20 }}%)</th>
                    <th style="width: 75px;">Keterampilan<br>({{ $bobot['KTR'] ?? 20 }}%)</th>
                    <th style="width: 60px;">UTS<br>({{ $bobot['UTS'] ?? 20 }}%)</th>
                    <th style="width: 60px;">UAS<br>({{ $bobot['UAS'] ?? 20 }}%)</th>
                    <th style="width: 75px;">Nilai Akhir</th>
                    <th style="width: 80px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanNilai as $item)
                    <tr>
                        <td>{{ $item['no'] }}</td>
                        <td>{{ $item['siswa']?->nis ?? '-' }}</td>
                        <td class="text-start">{{ $item['siswa']?->nama ?? '-' }}</td>
                        <td>{{ $item['nilai_harian'] !== null ? number_format($item['nilai_harian'], 2) : '-' }}</td>
                        <td>{{ $item['nilai_tugas'] !== null ? number_format($item['nilai_tugas'], 2) : '-' }}</td>
                        <td>{{ $item['nilai_keterampilan'] !== null ? number_format($item['nilai_keterampilan'], 2) : '-' }}</td>
                        <td>{{ $item['nilai_uts'] !== null ? number_format($item['nilai_uts'], 2) : '-' }}</td>
                        <td>{{ $item['nilai_uas'] !== null ? number_format($item['nilai_uas'], 2) : '-' }}</td>
                        <td class="fw-bold">{{ number_format($item['rata_rata'], 2) }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 20px;">Data nilai belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah
                    <div class="ttd-space"></div>
                    <strong><u>{{ $kepalaSekolah?->nama ?? 'Kepala Sekolah' }}</u></strong><br>
                    NIP. {{ $kepalaSekolah?->nip ?? '-' }}
                </td>
                <td>
                    {{ $sekolah?->kabupaten_kota ?? 'Tempat' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                    Guru Mata Pelajaran
                    <div class="ttd-space"></div>
                    <strong><u>{{ $guru->nama }}</u></strong><br>
                    NIP. {{ $guru->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
