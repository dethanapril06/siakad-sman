@php
    $mengajar ??= null;
@endphp

<div class="mb-3">
    <label class="form-label" for="semester_id">Semester</label>
    <select class="form-select @error('semester_id') is-invalid @enderror" id="semester_id" name="semester_id" autofocus>
        <option value="">Pilih semester</option>
        @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" @selected((string) old('semester_id', $mengajar?->semester_id) === (string) $semester->id)>
                {{ ucfirst($semester->nama) }} - {{ $semester->tahunAkademik?->nama }}
            </option>
        @endforeach
    </select>
    @error('semester_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="guru_id">Guru</label>
    <select class="form-select @error('guru_id') is-invalid @enderror" id="guru_id" name="guru_id">
        <option value="">Pilih guru</option>
        @foreach ($gurus as $guru)
            <option value="{{ $guru->id }}" @selected((string) old('guru_id', $mengajar?->guru_id) === (string) $guru->id)>
                {{ $guru->nama }}{{ $guru->nip ? ' - ' . $guru->nip : '' }}
            </option>
        @endforeach
    </select>
    @error('guru_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="kelas_akademik_id">Kelas Akademik</label>
    <select class="form-select @error('kelas_akademik_id') is-invalid @enderror" id="kelas_akademik_id" name="kelas_akademik_id">
        <option value="">Pilih kelas akademik</option>
        @foreach ($kelasAkademiks as $kelasAkademik)
            <option value="{{ $kelasAkademik->id }}" @selected((string) old('kelas_akademik_id', $mengajar?->kelas_akademik_id) === (string) $kelasAkademik->id)>
                {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
            </option>
        @endforeach
    </select>
    @error('kelas_akademik_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="mata_pelajaran_id">Mata Pelajaran</label>
    <select class="form-select @error('mata_pelajaran_id') is-invalid @enderror" id="mata_pelajaran_id" name="mata_pelajaran_id">
        <option value="">Pilih mata pelajaran</option>
        @foreach ($mataPelajarans as $mataPelajaran)
            <option value="{{ $mataPelajaran->id }}" @selected((string) old('mata_pelajaran_id', $mengajar?->mata_pelajaran_id) === (string) $mataPelajaran->id)>
                {{ $mataPelajaran->nama }}{{ $mataPelajaran->kode ? ' - ' . $mataPelajaran->kode : '' }}
            </option>
        @endforeach
    </select>
    @error('mata_pelajaran_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
