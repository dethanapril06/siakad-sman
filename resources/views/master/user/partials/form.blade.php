@php
    $user ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="name">Nama Lengkap</label>
    <input
        type="text"
        class="form-control @error('name') is-invalid @enderror"
        id="name"
        name="name"
        value="{{ old('name', $user?->name) }}"
        placeholder="Masukkan nama lengkap"
        autofocus
        required
    />
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        id="email"
        name="email"
        value="{{ old('email', $user?->email) }}"
        placeholder="contoh@sman1kupangtimur.sch.id"
        required
    />
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="role_id">Role / Hak Akses</label>
    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
        <option value="">Pilih Role</option>
        @foreach ($roles as $role)
            @php
                $roleLabels = [
                    'pegawai_tu' => 'Pegawai Tata Usaha (TU)',
                    'guru' => 'Guru',
                    'siswa' => 'Siswa',
                    'kepala_sekolah' => 'Kepala Sekolah',
                ];
                $label = $roleLabels[$role->name] ?? ucwords(str_replace('_', ' ', $role->name));
            @endphp
            <option value="{{ $role->id }}" @selected(old('role_id', $user?->role_id) == $role->id)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('role_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="is_active">Status Akun</label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        <option value="1" @selected(old('is_active', $user ? ($user->is_active ? '1' : '0') : '1') === '1')>Aktif</option>
        <option value="0" @selected(old('is_active', $user ? ($user->is_active ? '1' : '0') : '1') === '0')>Nonaktif</option>
    </select>
    @error('is_active')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<hr class="my-4" />
<h6 class="mb-3">Keamanan & Kata Sandi</h6>

<div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        id="password"
        name="password"
        autocomplete="new-password"
        placeholder="{{ $user ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}"
        {{ $user ? '' : 'required' }}
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
        {{ $user ? '' : 'required' }}
    />
</div>
