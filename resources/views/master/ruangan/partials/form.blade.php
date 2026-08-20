@php
    $ruangan ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="kode">Kode Ruangan</label>
    <input
        type="text"
        class="form-control text-uppercase @error('kode') is-invalid @enderror"
        id="kode"
        name="kode"
        value="{{ old('kode', $ruangan?->kode) }}"
        placeholder="Contoh: R-101"
        autofocus
    />
    @error('kode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Ruangan</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $ruangan?->nama) }}"
        placeholder="Contoh: Ruang Kelas 101"
    />
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jenis">Jenis Ruangan</label>
    <select class="form-select @error('jenis') is-invalid @enderror" id="jenis" name="jenis">
        <option value="">Pilih jenis</option>
        <option value="kelas" @selected(old('jenis', $ruangan?->jenis) === 'kelas')>Kelas</option>
        <option value="laboratorium" @selected(old('jenis', $ruangan?->jenis) === 'laboratorium')>Laboratorium</option>
        <option value="lainnya" @selected(old('jenis', $ruangan?->jenis) === 'lainnya')>Lainnya</option>
    </select>
    @error('jenis')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="kapasitas">Kapasitas</label>
    <input
        type="number"
        min="1"
        max="1000"
        class="form-control @error('kapasitas') is-invalid @enderror"
        id="kapasitas"
        name="kapasitas"
        value="{{ old('kapasitas', $ruangan?->kapasitas) }}"
        placeholder="Contoh: 36"
    />
    @error('kapasitas')
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
            @checked(old('is_active', $ruangan?->is_active ?? true))
        />
        <label class="form-check-label" for="is_active">Ruangan aktif</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
