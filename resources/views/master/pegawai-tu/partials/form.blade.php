@php
    $pegawaiTu ??= null;
@endphp

<h6 class="mb-3">Data Akun Login</h6>

<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        id="email"
        name="email"
        value="{{ old('email', $pegawaiTu?->user?->email) }}"
        placeholder="nama@sman1kupangtimur.sch.id"
        autofocus
        required
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
        placeholder="{{ $pegawaiTu ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}"
        {{ $pegawaiTu ? '' : 'required' }}
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
        {{ $pegawaiTu ? '' : 'required' }}
    />
</div>

<hr class="my-4" />
<h6 class="mb-3">Data Pribadi Pegawai Tata Usaha</h6>

<div class="mb-3">
    <label class="form-label" for="nip">NIP</label>
    <input
        type="text"
        class="form-control @error('nip') is-invalid @enderror"
        id="nip"
        name="nip"
        value="{{ old('nip', $pegawaiTu?->nip) }}"
        placeholder="Masukkan NIP (atau tanda - jika non-NIP)"
        required
    />
    @error('nip')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Lengkap Pegawai</label>
    <input
        type="text"
        class="form-control @error('nama') is-invalid @enderror"
        id="nama"
        name="nama"
        value="{{ old('nama', $pegawaiTu?->nama) }}"
        placeholder="Masukkan nama lengkap beserta gelar jika ada"
        required
    />
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
        <option value="">Pilih jenis kelamin</option>
        <option value="L" @selected(old('jenis_kelamin', $pegawaiTu?->jenis_kelamin) === 'L')>Laki-laki</option>
        <option value="P" @selected(old('jenis_kelamin', $pegawaiTu?->jenis_kelamin) === 'P')>Perempuan</option>
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
        value="{{ old('tempat_lahir', $pegawaiTu?->tempat_lahir) }}"
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
        value="{{ old('tanggal_lahir', $pegawaiTu?->tanggal_lahir?->format('Y-m-d')) }}"
    />
    @error('tanggal_lahir')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="no_hp">No. HP / WhatsApp</label>
    <input
        type="text"
        class="form-control @error('no_hp') is-invalid @enderror"
        id="no_hp"
        name="no_hp"
        value="{{ old('no_hp', $pegawaiTu?->no_hp) }}"
        placeholder="Contoh: 081234567890"
    />
    @error('no_hp')
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
        placeholder="Masukkan alamat domisili"
    >{{ old('alamat', $pegawaiTu?->alamat) }}</textarea>
    @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
