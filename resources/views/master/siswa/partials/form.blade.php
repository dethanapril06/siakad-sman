@php($siswa ??= null)

<h6 class="mb-3">Data Akun Login</h6>
<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $siswa?->user?->email) }}" placeholder="nama@email.com" autofocus />
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password" placeholder="{{ $siswa ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}" />
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password" />
</div>

<hr class="my-4" />
<h6 class="mb-3">Data Pribadi Siswa</h6>
@foreach ([
    ['nis', 'NIS', 'Masukkan NIS'],
    ['nisn', 'NISN', 'Masukkan NISN'],
    ['nama', 'Nama Siswa', 'Masukkan nama siswa'],
    ['tempat_lahir', 'Tempat Lahir', 'Masukkan tempat lahir'],
    ['nama_orang_tua', 'Nama Orang Tua', 'Masukkan nama orang tua'],
    ['no_hp_orang_tua', 'No. HP Orang Tua', 'Masukkan no. HP orang tua'],
] as [$field, $label, $placeholder])
    <div class="mb-3">
        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
        <input type="text" class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $siswa?->{$field}) }}" placeholder="{{ $placeholder }}" />
        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@endforeach

<div class="mb-3">
    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
        <option value="">Pilih jenis kelamin</option>
        <option value="L" @selected(old('jenis_kelamin', $siswa?->jenis_kelamin) === 'L')>Laki-laki</option>
        <option value="P" @selected(old('jenis_kelamin', $siswa?->jenis_kelamin) === 'P')>Perempuan</option>
    </select>
    @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa?->tanggal_lahir?->format('Y-m-d')) }}" />
    @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="status">Status</label>
    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
        @foreach (['aktif', 'lulus', 'pindah', 'nonaktif'] as $status)
            <option value="{{ $status }}" @selected(old('status', $siswa?->status ?? 'aktif') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label class="form-label" for="alamat">Alamat</label>
    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat">{{ old('alamat', $siswa?->alamat) }}</textarea>
    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
