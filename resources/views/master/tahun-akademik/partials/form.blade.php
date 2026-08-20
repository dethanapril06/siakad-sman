@php
    $tahunAkademik ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="nama">Nama Tahun Akademik</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $tahunAkademik?->nama) }}"
        placeholder="Contoh: 2026/2027"
        autofocus
    />
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
        value="{{ old('tanggal_mulai', $tahunAkademik?->tanggal_mulai?->format('Y-m-d')) }}"
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
        value="{{ old('tanggal_selesai', $tahunAkademik?->tanggal_selesai?->format('Y-m-d')) }}"
    />
    @error('tanggal_selesai')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input
            class="form-check-input @error('is_active') is-invalid @enderror"
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(old('is_active', $tahunAkademik?->is_active))
        />
        <label class="form-check-label" for="is_active">Jadikan tahun akademik aktif</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
