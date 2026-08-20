@php
    $jadwal ??= null;
    $jamMulai = old('jam_mulai', $jadwal?->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : null);
    $jamSelesai = old('jam_selesai', $jadwal?->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : null);
@endphp

<div class="mb-3">
    <label class="form-label" for="mengajar_id">Penugasan Mengajar</label>
    <select class="form-select @error('mengajar_id') is-invalid @enderror" id="mengajar_id" name="mengajar_id" autofocus>
        <option value="">Pilih penugasan mengajar</option>
        @foreach ($mengajars as $mengajar)
            <option value="{{ $mengajar->id }}" @selected((string) old('mengajar_id', $jadwal?->mengajar_id) === (string) $mengajar->id)>
                {{ $mengajar->guru?->nama ?? '-' }} |
                {{ $mengajar->mataPelajaran?->nama ?? '-' }} |
                {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }} |
                {{ ucfirst($mengajar->semester?->nama ?? '-') }} - {{ $mengajar->semester?->tahunAkademik?->nama ?? '-' }}
            </option>
        @endforeach
    </select>
    @error('mengajar_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="ruangan_id">Ruangan</label>
    <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id" name="ruangan_id">
        <option value="">Tanpa ruangan</option>
        @foreach ($ruangans as $ruangan)
            <option value="{{ $ruangan->id }}" @selected((string) old('ruangan_id', $jadwal?->ruangan_id) === (string) $ruangan->id)>
                {{ $ruangan->nama }}{{ $ruangan->kode ? ' - ' . $ruangan->kode : '' }}
            </option>
        @endforeach
    </select>
    @error('ruangan_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="hari">Hari</label>
    <select class="form-select @error('hari') is-invalid @enderror" id="hari" name="hari">
        <option value="">Pilih hari</option>
        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
            <option value="{{ $hari }}" @selected(old('hari', $jadwal?->hari) === $hari)>{{ ucfirst($hari) }}</option>
        @endforeach
    </select>
    @error('hari')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="jam_mulai">Jam Mulai</label>
    <input
        type="time"
        class="form-control @error('jam_mulai') is-invalid @enderror"
        id="jam_mulai"
        name="jam_mulai"
        value="{{ $jamMulai }}"
    />
    @error('jam_mulai')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="jam_selesai">Jam Selesai</label>
    <input
        type="time"
        class="form-control @error('jam_selesai') is-invalid @enderror"
        id="jam_selesai"
        name="jam_selesai"
        value="{{ $jamSelesai }}"
    />
    @error('jam_selesai')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
