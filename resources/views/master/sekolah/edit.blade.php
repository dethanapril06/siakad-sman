@extends($layout)

@section('title', 'Pengaturan Sekolah & Kop Laporan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Master /</span> Pengaturan Sekolah & Kop Laporan</h4>
            <span class="text-muted">Kelola identitas resmi sekolah, pejabat kepala sekolah, dan format kop surat laporan/rapor</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="bx bx-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <i class="bx bx-error-circle me-1"></i>
            Terdapat beberapa kesalahan pengisian. Silakan periksa kembali formulir di bawah.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Pratinjau Kop Surat Live --}}
        <div class="col-12 mb-4">
            <div class="card border-primary border-1 shadow-none bg-label-secondary">
                <div class="card-header pb-2 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary"><i class="bx bx-show me-1"></i> Pratinjau Kop Laporan & Rapor</span>
                    <small class="text-muted">Tampilan resmi pada lembar cetak</small>
                </div>
                <div class="card-body bg-white m-3 p-4 rounded border text-center">
                    <div style="border-bottom: 3px double #000; padding-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            @if ($sekolah->logo)
                                <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" style="height: 65px; object-fit: contain;">
                            @endif
                            <div>
                                <h6 class="mb-0 fw-semibold text-uppercase text-dark" style="font-size: 11pt; letter-spacing: 0.5px;">{{ $sekolah->nama_instansi }}</h6>
                                <h6 class="mb-0 fw-semibold text-uppercase text-dark" style="font-size: 11pt; letter-spacing: 0.5px;">{{ $sekolah->nama_dinas }}</h6>
                                <h4 class="mb-1 fw-bold text-uppercase text-dark" style="font-size: 14pt; letter-spacing: 1px;">{{ $sekolah->nama_sekolah }}</h4>
                                <p class="mb-0 text-muted" style="font-size: 8.5pt;">
                                    {{ $sekolah->alamat }} | NPSN: {{ $sekolah->npsn ?? '-' }} | Akreditasi: {{ $sekolah->akreditasi ?? '-' }}
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 8.5pt;">
                                    Telepon: {{ $sekolah->telepon ?? '-' }} | Email: {{ $sekolah->email ?? '-' }} | Website: {{ $sekolah->website ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pengaturan --}}
        <div class="col-12">
            <form action="{{ route('pegawai-tu.master.sekolah.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- 1. Identitas Lembaga & Kop --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-label-primary">
                                <h5 class="mb-0 text-primary"><i class="bx bx-buildings me-2"></i>Identitas Kop & Instansi</h5>
                            </div>
                            <div class="card-body pt-4">
                                <div class="mb-3">
                                    <label class="form-label" for="nama_instansi">Nama Pemerintah / Instansi Atas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror" id="nama_instansi" name="nama_instansi" value="{{ old('nama_instansi', $sekolah->nama_instansi) }}" required placeholder="Contoh: PEMERINTAH PROVINSI NUSA TENGGARA TIMUR" />
                                    @error('nama_instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Baris pertama pada Kop Surat</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="nama_dinas">Nama Dinas Pendidikan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_dinas') is-invalid @enderror" id="nama_dinas" name="nama_dinas" value="{{ old('nama_dinas', $sekolah->nama_dinas) }}" required placeholder="Contoh: DINAS PENDIDIKAN DAN KEBUDAYAAN" />
                                    @error('nama_dinas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Baris kedua pada Kop Surat</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="nama_sekolah">Nama Sekolah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" required placeholder="Contoh: SMA NEGERI 1 KUPANG TIMUR" />
                                    @error('nama_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Baris utama judul sekolah pada Kop Surat & Rapor</small>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="npsn">NPSN</label>
                                        <input type="text" class="form-control @error('npsn') is-invalid @enderror" id="npsn" name="npsn" value="{{ old('npsn', $sekolah->npsn) }}" placeholder="Contoh: 50300123" />
                                        @error('npsn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="akreditasi">Akreditasi</label>
                                        <input type="text" class="form-control @error('akreditasi') is-invalid @enderror" id="akreditasi" name="akreditasi" value="{{ old('akreditasi', $sekolah->akreditasi) }}" placeholder="Contoh: A (Unggul)" />
                                        @error('akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="logo">Logo Sekolah / Logo Daerah (Opsional)</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*" />
                                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Format: PNG, JPG, WEBP, SVG (Maks. 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Pejabat Kepala Sekolah & Tanda Tangan --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-label-info">
                                <h5 class="mb-0 text-info"><i class="bx bx-user-check me-2"></i>Pejabat Kepala Sekolah & Titimangsa</h5>
                            </div>
                            <div class="card-body pt-4">
                                <div class="mb-3">
                                    <label class="form-label" for="pilih_guru">Pilih Cepat Dari Data Guru</label>
                                    <select class="form-select" id="pilih_guru" onchange="autoFillKepalaSekolah(this)">
                                        <option value="">-- Pilih Guru Untuk Mengisi Otomatis --</option>
                                        @foreach ($gurus as $guru)
                                            <option value="{{ $guru->nama }}" data-nip="{{ $guru->nip }}">{{ $guru->nama }} (NIP. {{ $guru->nip ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Opsional: Memudahkan mengisi data kepala sekolah dari guru yang ada</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="kepala_sekolah_nama">Nama Lengkap Kepala Sekolah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kepala_sekolah_nama') is-invalid @enderror" id="kepala_sekolah_nama" name="kepala_sekolah_nama" value="{{ old('kepala_sekolah_nama', $sekolah->kepala_sekolah_nama) }}" required placeholder="Contoh: Drs. Yakob Manafe, M.Pd" />
                                    @error('kepala_sekolah_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Nama yang tercetak di kolom tanda tangan 'Mengetahui, Kepala Sekolah'</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="kepala_sekolah_nip">NIP Kepala Sekolah</label>
                                    <input type="text" class="form-control @error('kepala_sekolah_nip') is-invalid @enderror" id="kepala_sekolah_nip" name="kepala_sekolah_nip" value="{{ old('kepala_sekolah_nip', $sekolah->kepala_sekolah_nip) }}" placeholder="Contoh: 197501012000011001" />
                                    @error('kepala_sekolah_nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="kepala_sekolah_ttd_lokasi">Tempat / Titimangsa Tanda Tangan</label>
                                    <input type="text" class="form-control @error('kepala_sekolah_ttd_lokasi') is-invalid @enderror" id="kepala_sekolah_ttd_lokasi" name="kepala_sekolah_ttd_lokasi" value="{{ old('kepala_sekolah_ttd_lokasi', $sekolah->kepala_sekolah_ttd_lokasi) }}" placeholder="Contoh: Kupang Timur" />
                                    @error('kepala_sekolah_ttd_lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Nama kota/wilayah untuk tanggal cetak (misal: "Kupang Timur, 19 Desember 2026")</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Kontak & Alamat Lengkap --}}
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-label-secondary">
                                <h5 class="mb-0"><i class="bx bx-map-pin me-2"></i>Alamat & Kontak Sekolah</h5>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="alamat">Alamat Jalan / Gedung</label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2" placeholder="Contoh: Jl. Timor Raya Km. 25">{{ old('alamat', $sekolah->alamat) }}</textarea>
                                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="kelurahan">Kelurahan / Desa</label>
                                        <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $sekolah->kelurahan) }}" placeholder="Contoh: Tuatuka" />
                                        @error('kelurahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="kecamatan">Kecamatan</label>
                                        <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $sekolah->kecamatan) }}" placeholder="Contoh: Kupang Timur" />
                                        @error('kecamatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="kabupaten_kota">Kabupaten / Kota</label>
                                        <input type="text" class="form-control @error('kabupaten_kota') is-invalid @enderror" id="kabupaten_kota" name="kabupaten_kota" value="{{ old('kabupaten_kota', $sekolah->kabupaten_kota) }}" placeholder="Contoh: Kabupaten Kupang" />
                                        @error('kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="provinsi">Provinsi</label>
                                        <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" value="{{ old('provinsi', $sekolah->provinsi) }}" placeholder="Contoh: Nusa Tenggara Timur" />
                                        @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="kode_pos">Kode Pos</label>
                                        <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $sekolah->kode_pos) }}" placeholder="Contoh: 85362" />
                                        @error('kode_pos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="telepon">Telepon / Fax</label>
                                        <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $sekolah->telepon) }}" placeholder="Contoh: (0380) 123456" />
                                        @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="email">Email Resmi</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $sekolah->email) }}" placeholder="Contoh: info@sman1kupangtimur.sch.id" />
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="website">Website Resmi</label>
                                        <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $sekolah->website) }}" placeholder="Contoh: www.sman1kupangtimur.sch.id" />
                                        @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($isPegawaiTu)
                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bx bx-save me-1"></i> Simpan Pengaturan Sekolah & Kop
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function autoFillKepalaSekolah(selectElem) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            if (selectedOption && selectedOption.value) {
                document.getElementById('kepala_sekolah_nama').value = selectedOption.value;
                document.getElementById('kepala_sekolah_nip').value = selectedOption.getAttribute('data-nip') || '';
            }
        }
    </script>
@endsection
