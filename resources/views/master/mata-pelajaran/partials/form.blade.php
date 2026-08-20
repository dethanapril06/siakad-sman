@php
    $mataPelajaran ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="kode">Kode Mata Pelajaran</label>
    <input
        type="text"
        class="form-control text-uppercase @error('kode') is-invalid @enderror"
        id="kode"
        name="kode"
        value="{{ old('kode', $mataPelajaran?->kode) }}"
        placeholder="Contoh: MTK"
        autofocus
    />
    @error('kode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Mata Pelajaran</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $mataPelajaran?->nama) }}"
        placeholder="Contoh: Matematika"
    />
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="kelompok">Kelompok</label>
    <input
        type="text"
        class="form-control @error('kelompok') is-invalid @enderror"
        id="kelompok"
        name="kelompok"
        value="{{ old('kelompok', $mataPelajaran?->kelompok) }}"
        placeholder="Contoh: Umum"
    />
    @error('kelompok')
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
            @checked(old('is_active', $mataPelajaran?->is_active ?? true))
        />
        <label class="form-check-label" for="is_active">Mata pelajaran aktif</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
