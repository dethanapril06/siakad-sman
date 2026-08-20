@extends($layout)

@section('title', 'Profil Saya')

@section('content')
    @php
        $user = auth()->user();
        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=696cff&color=fff&bold=true&size=160';
    @endphp

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun /</span> Profil Saya</h4>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('profile'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('profile') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Profil</a>
                </li>
            </ul>

            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>

                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                            src="{{ $avatarUrl }}"
                            alt="avatar {{ $user->name }}"
                            class="d-block rounded"
                            height="100"
                            width="100"
                        />
                        <div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="mb-0 text-muted">{{ $roleLabel }}</p>
                            <small class="text-muted">Avatar otomatis dibuat dari nama akun.</small>
                        </div>
                    </div>
                </div>

                <hr class="my-0" />

                <div class="card-body">
                    @if (! $profile)
                        <div class="alert alert-warning mb-0" role="alert">
                            Data pribadi untuk role {{ $roleLabel }} belum tersedia.
                        </div>
                    @else
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h6 class="mb-3">Data Akun Login</h6>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Nama Akun</label>
                                    <input
                                        class="form-control @error('name') is-invalid @enderror"
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $user->name) }}"
                                        autofocus
                                    />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        class="form-control @error('email') is-invalid @enderror"
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $user->email) }}"
                                    />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input
                                        class="form-control @error('password') is-invalid @enderror"
                                        type="password"
                                        id="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="Kosongkan jika tidak diganti"
                                    />
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <input
                                        class="form-control"
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Ulangi password baru"
                                    />
                                </div>
                            </div>

                            <hr class="my-4" />

                            <h6 class="mb-3">Data Pribadi</h6>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="nomor_induk">
                                        @if ($roleName === 'siswa')
                                            NIS
                                        @else
                                            NIP
                                        @endif
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nomor_induk"
                                        value="{{ $roleName === 'siswa' ? $profile->nis : $profile->nip }}"
                                        disabled
                                    />
                                </div>

                                @if ($roleName === 'siswa')
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="nisn">NISN</label>
                                        <input type="text" class="form-control" id="nisn" value="{{ $profile->nisn }}" disabled />
                                    </div>
                                @endif

                                <div class="mb-3 col-md-6">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input
                                        class="form-control @error('nama') is-invalid @enderror"
                                        type="text"
                                        id="nama"
                                        name="nama"
                                        value="{{ old('nama', $profile->nama) }}"
                                    />
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="">Pilih</option>
                                        <option value="L" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'L')>Laki-laki</option>
                                        <option value="P" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'P')>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        type="text"
                                        id="tempat_lahir"
                                        name="tempat_lahir"
                                        value="{{ old('tempat_lahir', $profile->tempat_lahir) }}"
                                    />
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        type="date"
                                        id="tanggal_lahir"
                                        name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', optional($profile->tanggal_lahir)->format('Y-m-d')) }}"
                                    />
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if (in_array($roleName, ['pegawai_tu', 'guru', 'kepala_sekolah'], true))
                                    <div class="mb-3 col-md-6">
                                        <label for="no_hp" class="form-label">No. HP</label>
                                        <input
                                            class="form-control @error('no_hp') is-invalid @enderror"
                                            type="text"
                                            id="no_hp"
                                            name="no_hp"
                                            value="{{ old('no_hp', $profile->no_hp) }}"
                                        />
                                        @error('no_hp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                @if ($roleName === 'siswa')
                                    <div class="mb-3 col-md-6">
                                        <label for="nama_orang_tua" class="form-label">Nama Orang Tua</label>
                                        <input
                                            class="form-control @error('nama_orang_tua') is-invalid @enderror"
                                            type="text"
                                            id="nama_orang_tua"
                                            name="nama_orang_tua"
                                            value="{{ old('nama_orang_tua', $profile->nama_orang_tua) }}"
                                        />
                                        @error('nama_orang_tua')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="no_hp_orang_tua" class="form-label">No. HP Orang Tua</label>
                                        <input
                                            class="form-control @error('no_hp_orang_tua') is-invalid @enderror"
                                            type="text"
                                            id="no_hp_orang_tua"
                                            name="no_hp_orang_tua"
                                            value="{{ old('no_hp_orang_tua', $profile->no_hp_orang_tua) }}"
                                        />
                                        @error('no_hp_orang_tua')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                <div class="mb-3 col-md-12">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea
                                        class="form-control @error('alamat') is-invalid @enderror"
                                        id="alamat"
                                        name="alamat"
                                        rows="3"
                                    >{{ old('alamat', $profile->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                <a href="{{ route(match ($roleName) {
                                    'pegawai_tu' => 'pegawai-tu.dashboard',
                                    'guru' => 'guru.dashboard',
                                    'siswa' => 'siswa.dashboard',
                                    'kepala_sekolah' => 'kepala-sekolah.dashboard',
                                }) }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
