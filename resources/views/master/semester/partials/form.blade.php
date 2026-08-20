@php
    $semester ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="tahun_akademik_id">Tahun Akademik</label>
    <select
        class="form-select @error('tahun_akademik_id') is-invalid @enderror"
        id="tahun_akademik_id"
        name="tahun_akademik_id"
        autofocus
    >
        <option value="">Pilih tahun akademik</option>
        @foreach ($tahunAkademiks as $tahunAkademik)
            <option value="{{ $tahunAkademik->id }}" @selected((string) old('tahun_akademik_id', $semester?->tahun_akademik_id) === (string) $tahunAkademik->id)>
                {{ $tahunAkademik->nama }}
            </option>
        @endforeach
    </select>
    @error('tahun_akademik_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Semester</label>
    <select class="form-select @error('nama') is-invalid @enderror" id="nama" name="nama">
        <option value="">Pilih semester</option>
        <option value="ganjil" @selected(old('nama', $semester?->nama) === 'ganjil')>Ganjil</option>
        <option value="genap" @selected(old('nama', $semester?->nama) === 'genap')>Genap</option>
    </select>
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
    <input
        type="date"
        class="form-control @error('tanggal_mulai') is-invalid @enderror"
        id="tanggal_mulai"
        name="tanggal_mulai"
        value="{{ old('tanggal_mulai', $semester?->tanggal_mulai?->format('Y-m-d')) }}"
    />
    @error('tanggal_mulai')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
    <input
        type="date"
        class="form-control @error('tanggal_selesai') is-invalid @enderror"
        id="tanggal_selesai"
        name="tanggal_selesai"
        value="{{ old('tanggal_selesai', $semester?->tanggal_selesai?->format('Y-m-d')) }}"
    />
    @error('tanggal_selesai')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal_rapor">Tanggal Pembagian Rapor</label>
    <input
        type="date"
        class="form-control @error('tanggal_rapor') is-invalid @enderror"
        id="tanggal_rapor"
        name="tanggal_rapor"
        value="{{ old('tanggal_rapor', $semester?->tanggal_rapor?->format('Y-m-d')) }}"
    />
    <small class="text-muted">Tanggal resmi yang akan dicantumkan pada lembar rapor siswa.</small>
    @error('tanggal_rapor')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input
            class="form-check-input @error('is_active') is-invalid @enderror"
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(old('is_active', $semester?->is_active))
        />
        <label class="form-check-label" for="is_active">Jadikan semester aktif</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input
            class="form-check-input @error('is_rapor_open') is-invalid @enderror"
            type="checkbox"
            id="is_rapor_open"
            name="is_rapor_open"
            value="1"
            @checked(old('is_rapor_open', $semester?->is_rapor_open))
        />
        <label class="form-check-label" for="is_rapor_open">Buka akses cetak rapor untuk Wali Kelas & Siswa</label>
        @error('is_rapor_open')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
