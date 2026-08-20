@php
    $kelasAkademik ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="kelas_id">Kelas</label>
    <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id" name="kelas_id" autofocus>
        <option value="">Pilih kelas</option>
        @foreach ($kelas as $item)
            <option value="{{ $item->id }}" @selected((string) old('kelas_id', $kelasAkademik?->kelas_id) === (string) $item->id)>
                {{ $item->nama_lengkap }}
            </option>
        @endforeach
    </select>
    @error('kelas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tahun_akademik_id">Tahun Akademik</label>
    <select class="form-select @error('tahun_akademik_id') is-invalid @enderror" id="tahun_akademik_id" name="tahun_akademik_id">
        <option value="">Pilih tahun akademik</option>
        @foreach ($tahunAkademiks as $tahunAkademik)
            <option value="{{ $tahunAkademik->id }}" @selected((string) old('tahun_akademik_id', $kelasAkademik?->tahun_akademik_id) === (string) $tahunAkademik->id)>
                {{ $tahunAkademik->nama }}
            </option>
        @endforeach
    </select>
    @error('tahun_akademik_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="wali_kelas_id">Wali Kelas</label>
    <select class="form-select @error('wali_kelas_id') is-invalid @enderror" id="wali_kelas_id" name="wali_kelas_id">
        <option value="">Belum ditentukan</option>
        @foreach ($gurus as $guru)
            <option value="{{ $guru->id }}" @selected((string) old('wali_kelas_id', $kelasAkademik?->wali_kelas_id) === (string) $guru->id)>
                {{ $guru->nama }}{{ $guru->nip ? ' - ' . $guru->nip : '' }}
            </option>
        @endforeach
    </select>
    @error('wali_kelas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
