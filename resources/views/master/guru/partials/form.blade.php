@php
    $guru ??= null;
@endphp

<h6 class="mb-3">Data Akun Login</h6>

<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        id="email"
        name="email"
        value="{{ old('email', $guru?->user?->email) }}"
        placeholder="nama@email.com"
        autofocus
    />
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        id="password"
        name="password"
        autocomplete="new-password"
        placeholder="{{ $guru ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}"
    />
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
    <input
        type="password"
        class="form-control"
        id="password_confirmation"
        name="password_confirmation"
        autocomplete="new-password"
        placeholder="Ulangi password"
    />
</div>

<hr class="my-4" />
<h6 class="mb-3">Data Pribadi Guru</h6>

<div class="mb-3">
    <label class="form-label" for="nip">NIP</label>
    <input
        type="text"
        class="form-control @error('nip') is-invalid @enderror"
        id="nip"
        name="nip"
        value="{{ old('nip', $guru?->nip) }}"
        placeholder="Masukkan NIP"
    />
    @error('nip')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Guru</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $guru?->nama) }}"
        placeholder="Masukkan nama guru"
    />
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
        <option value="">Pilih jenis kelamin</option>
        <option value="L" @selected(old('jenis_kelamin', $guru?->jenis_kelamin) === 'L')>Laki-laki</option>
        <option value="P" @selected(old('jenis_kelamin', $guru?->jenis_kelamin) === 'P')>Perempuan</option>
    </select>
    @error('jenis_kelamin')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
    <input
        type="text"
        class="form-control @error('tempat_lahir') is-invalid @enderror"
        id="tempat_lahir"
        name="tempat_lahir"
        value="{{ old('tempat_lahir', $guru?->tempat_lahir) }}"
        placeholder="Masukkan tempat lahir"
    />
    @error('tempat_lahir')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
    <input
        type="date"
        class="form-control @error('tanggal_lahir') is-invalid @enderror"
        id="tanggal_lahir"
        name="tanggal_lahir"
        value="{{ old('tanggal_lahir', $guru?->tanggal_lahir?->format('Y-m-d')) }}"
    />
    @error('tanggal_lahir')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="no_hp">No. HP</label>
    <input
        type="text"
        class="form-control @error('no_hp') is-invalid @enderror"
        id="no_hp"
        name="no_hp"
        value="{{ old('no_hp', $guru?->no_hp) }}"
        placeholder="Masukkan no. HP"
    />
    @error('no_hp')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="status">Status</label>
    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
        <option value="aktif" @selected(old('status', $guru?->status ?? 'aktif') === 'aktif')>Aktif</option>
        <option value="nonaktif" @selected(old('status', $guru?->status) === 'nonaktif')>Nonaktif</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="alamat">Alamat</label>
    <textarea
        class="form-control @error('alamat') is-invalid @enderror"
        id="alamat"
        name="alamat"
        rows="3"
        placeholder="Masukkan alamat"
    >{{ old('alamat', $guru?->alamat) }}</textarea>
    @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
