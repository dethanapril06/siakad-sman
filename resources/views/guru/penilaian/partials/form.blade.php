@php
    $penilaian ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="mengajar_id">Mengajar</label>
    <select class="form-select @error('mengajar_id') is-invalid @enderror" id="mengajar_id" name="mengajar_id" autofocus>
        <option value="">Pilih mengajar</option>
        @foreach ($mengajars as $mengajar)
            <option value="{{ $mengajar->id }}" @selected((string) old('mengajar_id', $penilaian?->mengajar_id ?? $selectedMengajarId ?? null) === (string) $mengajar->id)>
                {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
                - {{ ucfirst($mengajar->semester?->nama ?? '-') }} {{ $mengajar->semester?->tahunAkademik?->nama ?? '' }}
            </option>
        @endforeach
    </select>
    @error('mengajar_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jenis_nilai_id">Jenis Nilai</label>
    <select class="form-select @error('jenis_nilai_id') is-invalid @enderror" id="jenis_nilai_id" name="jenis_nilai_id">
        <option value="">Pilih jenis nilai</option>
        @foreach ($jenisNilais as $jenisNilai)
            <option value="{{ $jenisNilai->id }}" @selected((string) old('jenis_nilai_id', $penilaian?->jenis_nilai_id) === (string) $jenisNilai->id)>
                {{ $jenisNilai->nama }}
            </option>
        @endforeach
    </select>
    @error('jenis_nilai_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="judul">Judul Penilaian</label>
    <input
        type="text"
        class="form-control @error('judul') is-invalid @enderror"
        id="judul"
        name="judul"
        value="{{ old('judul', $penilaian?->judul) }}"
        placeholder="Contoh: Ulangan Harian 1"
    />
    @error('judul')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal">Tanggal</label>
    <input
        type="date"
        class="form-control @error('tanggal') is-invalid @enderror"
        id="tanggal"
        name="tanggal"
        value="{{ old('tanggal', $penilaian?->tanggal?->format('Y-m-d')) }}"
    />
    @error('tanggal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="keterangan">Keterangan</label>
    <textarea
        class="form-control @error('keterangan') is-invalid @enderror"
        id="keterangan"
        name="keterangan"
        rows="4"
        placeholder="Opsional"
    >{{ old('keterangan', $penilaian?->keterangan) }}</textarea>
    @error('keterangan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
