@php($kelas ??= null)

<div class="mb-3">
    <label class="form-label" for="tingkat">Tingkat</label>
    <select class="form-select @error('tingkat') is-invalid @enderror" id="tingkat" name="tingkat" autofocus>
        <option value="">Pilih tingkat</option>
        @foreach (['X', 'XI', 'XII'] as $tingkat)
            <option value="{{ $tingkat }}" @selected(old('tingkat', $kelas?->tingkat) === $tingkat)>{{ $tingkat }}</option>
        @endforeach
    </select>
    @error('tingkat')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jurusan_id">Jurusan</label>
    <select class="form-select @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id">
        <option value="">Tanpa jurusan</option>
        @foreach ($jurusans as $jurusan)
            <option value="{{ $jurusan->id }}" @selected((string) old('jurusan_id', $kelas?->jurusan_id) === (string) $jurusan->id)>
                {{ $jurusan->nama }}
            </option>
        @endforeach
    </select>
    @error('jurusan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nama">Nama Kelas</label>
    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $kelas?->nama) }}" placeholder="Contoh: 1" />
    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $kelas?->is_active ?? true)) />
        <label class="form-check-label" for="is_active">Kelas aktif</label>
        @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
