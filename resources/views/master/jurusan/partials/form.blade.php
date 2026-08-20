@php
    $jurusan ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="kode">Kode Jurusan</label>
    <input
        type="text"
        class="form-control text-uppercase @error('kode') is-invalid @enderror"
        id="kode"
        name="kode"
        value="{{ old('kode', $jurusan?->kode) }}"
        placeholder="Contoh: IPA"
        autofocus
    />
    @error('kode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Jurusan</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $jurusan?->nama) }}"
        placeholder="Contoh: Ilmu Pengetahuan Alam"
    />
    @error('nama')
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
            @checked(old('is_active', $jurusan?->is_active ?? true))
        />
        <label class="form-check-label" for="is_active">Jurusan aktif</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
