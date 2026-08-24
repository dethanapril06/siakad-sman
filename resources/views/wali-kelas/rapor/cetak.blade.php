<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Siswa - {{ $siswa->nama }} - {{ $semester?->tahunAkademik?->nama }}</title>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/boxicons.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Public Sans', Arial, sans-serif;
        }
        body {
            background-color: #f5f5f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .btn-print {
            background: #696cff;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-print:hover {
            background: #5f61e6;
        }
        .btn-back {
            background: #e7e7ff;
            color: #696cff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        .rapor-page {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 45px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-surat h2 {
            margin: 4px 0;
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-surat p {
            margin: 2px 0;
            font-size: 9pt;
            color: #444;
        }
        .rapor-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .rapor-title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .identitas-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }
        .identitas-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 9pt;
        }
        .content-table th, .content-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .content-table th {
            background-color: #f0f0f0;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: 700; }
        
        .section-title {
            font-size: 10pt;
            font-weight: 700;
            margin: 14px 0 6px 0;
            text-transform: uppercase;
        }
        .catatan-box {
            border: 1px solid #000;
            padding: 10px 14px;
            min-height: 50px;
            font-size: 9.5pt;
            margin-bottom: 20px;
            background: #fafafa;
        }
        .ttd-table {
            width: 100%;
            margin-top: 25px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 10px;
        }
        .ttd-space {
            height: 70px;
        }
        
        @media print {
            body {
                background: #fff;
                padding: 0;
                color: #000;
            }
            .no-print-bar {
                display: none !important;
            }
            .rapor-page {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                margin: 0;
            }
            .content-table th {
                background-color: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .catatan-box {
                background: #fff !important;
            }
        }
    </style>
</head>
<body>
@php
    $backRoute = auth()->user()?->isSiswa()
        ? route('siswa.rapor.index')
        : route('wali-kelas.rapor.index');
@endphp

    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Lembar Rapor Siswa</strong>
            <div style="font-size: 8.5pt; color: #777;">Format Siap Cetak (A4 Standard)</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ $backRoute }}" onclick="handleBack(event, '{{ $backRoute }}')" class="btn-back">Kembali</a>
            <button onclick="window.print()" class="btn-print">
                <i class="bx bx-printer"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="rapor-page">
        {{-- KOP SURAT RESMI --}}
        <div class="kop-surat">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 6px;">
                @if ($sekolahSetting->logo)
                    <img src="{{ asset('storage/' . $sekolahSetting->logo) }}" alt="Logo" style="height: 65px; object-fit: contain;">
                @endif
                <div>
                    <h4>{{ $sekolahSetting->nama_instansi }}</h4>
                    <h4>{{ $sekolahSetting->nama_dinas }}</h4>
                    <h2>{{ $sekolahSetting->nama_sekolah }}</h2>
                </div>
            </div>
            <p>{{ $sekolahSetting->alamat }} | NPSN: {{ $sekolahSetting->npsn ?? '-' }} | Telepon: {{ $sekolahSetting->telepon ?? '-' }}</p>
            <p>Website: {{ $sekolahSetting->website ?? '-' }} | Email: {{ $sekolahSetting->email ?? '-' }}</p>
        </div>

        {{-- JUDUL RAPOR --}}
        <div class="rapor-title">
            <h3>LAPORAN HASIL BELAJAR SISWA (RAPOR)</h3>
        </div>

        {{-- IDENTITAS PESERTA DIDIK --}}
        <table class="identitas-table">
            <tr>
                <td style="width: 18%;">Nama Peserta Didik</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%; font-weight: 700;">{{ $siswa->nama }}</td>
                <td style="width: 18%;">Kelas / Rombel</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%; font-weight: 700;">{{ $kelasAkademik?->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td>{{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $semester?->nama ? ucfirst($semester->nama) : '-' }}</td>
            </tr>
            <tr>
                <td>Jurusan / Peminatan</td>
                <td>:</td>
                <td>{{ $kelasAkademik?->kelas?->jurusan?->nama ?? '-' }}</td>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td>{{ $semester?->tahunAkademik?->nama ?? '-' }}</td>
            </tr>
        </table>

        {{-- TABEL CAPAIAN NILAI AKADEMIK --}}
        <div class="section-title">A. CAPAIAN KOMPETENSI AKADEMIK</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 5%;">No</th>
                    <th rowspan="2" style="width: 30%;">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 8%;">KKM</th>
                    <th colspan="2" style="width: 18%;">Nilai Akhir</th>
                    <th rowspan="2" style="width: 39%;">Capaian Kompetensi</th>
                </tr>
                <tr>
                    <th style="width: 9%;">Angka</th>
                    <th style="width: 9%;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nilaiMapel as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item['mata_pelajaran']->nama }}</strong>
                            <div style="font-size: 8pt; color: #555;">Kode: {{ $item['mata_pelajaran']->kode }}</div>
                        </td>
                        <td class="text-center">{{ $item['kkm'] }}</td>
                        <td class="text-center fw-bold">{{ $item['nilai_akhir'] ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $item['predikat'] }}</td>
                        <td style="font-size: 8pt; text-align: justify;">{{ $item['capaian_kompetensi'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data nilai mata pelajaran pada semester ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: 700;">
                    <td colspan="3" class="text-center">TOTAL NILAI / RATA-RATA KELAS</td>
                    <td class="text-center">{{ $totalNilai }}</td>
                    <td class="text-center">{{ $rataRata ?? '-' }}</td>
                    <td>
                        @if ($peringkat)
                            <strong>Peringkat ke-{{ $peringkat }}</strong> dari {{ $totalSiswa }} siswa
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- TABEL PRESENSI KEHADIRAN --}}
        <div class="section-title">B. REKAPITULASI KETIDAKHADIRAN</div>
        <table class="content-table" style="max-width: 450px;">
            <thead>
                <tr>
                    <th style="width: 70%;">Keterangan Presensi</th>
                    <th style="width: 30%;">Jumlah Hari / Jam</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sakit (S)</td>
                    <td class="text-center fw-bold">{{ $presensi['sakit'] ?? 0 }} hari</td>
                </tr>
                <tr>
                    <td>Izin (I)</td>
                    <td class="text-center fw-bold">{{ $presensi['izin'] ?? 0 }} hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan / Alpa (A)</td>
                    <td class="text-center fw-bold">{{ $presensi['alpa'] ?? 0 }} hari</td>
                </tr>
                <tr>
                    <td>Terlambat</td>
                    <td class="text-center fw-bold">{{ $presensi['terlambat'] ?? 0 }} kali</td>
                </tr>
            </tbody>
        </table>

        {{-- CATATAN WALI KELAS --}}
        <div class="section-title">C. CATATAN WALI KELAS</div>
        <div class="catatan-box">
            @if ($catatanWali?->catatan)
                {{ $catatanWali->catatan }}
            @else
                <em style="color: #888;">Tingkatkan terus motivasi belajar, kedisiplinan, dan keaktifan dalam kegiatan akademik maupun non-akademik di sekolah.</em>
            @endif

            @if ($catatanWali?->status_kenaikan)
                <div style="margin-top: 8px; font-weight: 700; color: #1a56db;">
                    Keputusan: {{ $catatanWali->status_kenaikan }}
                </div>
            @endif
        </div>

        {{-- TANDA TANGAN --}}
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>Orang Tua / Wali Murid
                    <div class="ttd-space"></div>
                    <strong>..................................................</strong>
                </td>
                <td>
                    Mengetahui,<br>Kepala Sekolah
                    <div class="ttd-space"></div>
                    <strong>{{ $sekolahSetting->kepala_sekolah_nama }}</strong><br>
                    <small>NIP. {{ $sekolahSetting->kepala_sekolah_nip ?? '........................' }}</small>
                </td>
                <td>
                    {{ $sekolahSetting->kepala_sekolah_ttd_lokasi ?? 'Kupang' }}, {{ $semester?->tanggal_rapor ? $semester->tanggal_rapor->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                    <div class="ttd-space"></div>
                    <strong>{{ $kelasAkademik?->guru?->nama ?? 'Nama Wali Kelas' }}</strong><br>
                    <small>NIP. {{ $kelasAkademik?->guru?->nip ?? '........................' }}</small>
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
