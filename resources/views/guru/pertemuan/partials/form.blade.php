@php
    $pertemuan ??= null;
    $nextPertemuanOptions ??= [];
    $selectedMengajarValue = old('mengajar_id', $pertemuan?->mengajar_id ?? $selectedMengajarId ?? null);
    $pertemuanKeValue = old(
        'pertemuan_ke',
        $pertemuan?->pertemuan_ke ?? ($selectedMengajarValue ? $nextPertemuanOptions[$selectedMengajarValue] ?? null : null)
    );
    $jamMulai = old('jam_mulai', $pertemuan?->jam_mulai ? substr($pertemuan->jam_mulai, 0, 5) : null);
    $jamSelesai = old('jam_selesai', $pertemuan?->jam_selesai ? substr($pertemuan->jam_selesai, 0, 5) : null);
    $jadwalOptions = $mengajars
        ->mapWithKeys(
            fn ($mengajar) => [
                $mengajar->id => [
                    'nextPertemuanKe' => $nextPertemuanOptions[$mengajar->id] ?? 1,
                    'jadwals' => $mengajar->jadwals
                        ->map(
                            fn ($jadwal) => [
                                'hari' => $jadwal->hari,
                                'jam_mulai' => substr($jadwal->jam_mulai, 0, 5),
                                'jam_selesai' => substr($jadwal->jam_selesai, 0, 5),
                                'ruangan' => $jadwal->ruangan?->nama,
                            ]
                        )
                        ->values(),
                ],
            ]
        )
        ->toArray();
@endphp

<div class="mb-3">
    <label class="form-label" for="mengajar_id">Mengajar</label>
    <select class="form-select @error('mengajar_id') is-invalid @enderror" id="mengajar_id" name="mengajar_id" autofocus>
        <option value="">Pilih mengajar</option>
        @foreach ($mengajars as $mengajar)
            <option value="{{ $mengajar->id }}" @selected((string) $selectedMengajarValue === (string) $mengajar->id)>
                {{ $mengajar->mataPelajaran?->nama ?? '-' }}
                - {{ $mengajar->kelasAkademik?->nama_lengkap ?? '-' }}
                - {{ ucfirst($mengajar->semester?->nama ?? '-') }} {{ $mengajar->semester?->tahunAkademik?->nama ?? '' }}
            </option>
        @endforeach
    </select>
    @error('mengajar_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="tanggal">Tanggal</label>
    <input
        type="date"
        class="form-control @error('tanggal') is-invalid @enderror"
        id="tanggal"
        name="tanggal"
        value="{{ old('tanggal', $pertemuan?->tanggal?->format('Y-m-d')) }}"
    />
    @error('tanggal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="pertemuan_ke">Pertemuan Ke</label>
        <input
            type="number"
            min="1"
            max="100"
            class="form-control @error('pertemuan_ke') is-invalid @enderror"
            id="pertemuan_ke"
            name="pertemuan_ke"
            value="{{ $pertemuanKeValue }}"
            placeholder="Otomatis"
            readonly
        />
        @error('pertemuan_ke')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="jam_mulai">Jam Mulai</label>
        <input
            type="time"
            class="form-control @error('jam_mulai') is-invalid @enderror"
            id="jam_mulai"
            name="jam_mulai"
            value="{{ $jamMulai }}"
            readonly
        />
        @error('jam_mulai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="jam_selesai">Jam Selesai</label>
        <input
            type="time"
            class="form-control @error('jam_selesai') is-invalid @enderror"
            id="jam_selesai"
            name="jam_selesai"
            value="{{ $jamSelesai }}"
            readonly
        />
        @error('jam_selesai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="alert alert-info" id="jadwal-info" role="alert">
    Pilih mengajar dan tanggal, lalu sistem akan mengambil jam dari jadwal yang sesuai.
</div>

<div class="mb-4">
    <label class="form-label" for="materi">Materi</label>
    <textarea
        class="form-control @error('materi') is-invalid @enderror"
        id="materi"
        name="materi"
        rows="4"
        placeholder="Ringkasan materi pertemuan"
    >{{ old('materi', $pertemuan?->materi) }}</textarea>
    @error('materi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jadwalOptions = @json($jadwalOptions);
            const hariIndonesia = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
            const isEdit = @json((bool) $pertemuan);
            const originalMengajarId = @json((string) ($pertemuan?->mengajar_id ?? ''));

            const mengajarSelect = document.getElementById('mengajar_id');
            const tanggalInput = document.getElementById('tanggal');
            const pertemuanKeInput = document.getElementById('pertemuan_ke');
            const jamMulaiInput = document.getElementById('jam_mulai');
            const jamSelesaiInput = document.getElementById('jam_selesai');
            const jadwalInfo = document.getElementById('jadwal-info');

            function getHariFromTanggal(tanggal) {
                if (! tanggal) {
                    return null;
                }

                return hariIndonesia[new Date(tanggal + 'T00:00:00').getDay()];
            }

            function updateJadwalFields() {
                const selected = jadwalOptions[mengajarSelect.value];
                const jadwals = selected ? selected.jadwals : [];
                const hari = getHariFromTanggal(tanggalInput.value);
                const jadwal = jadwals.find((item) => item.hari === hari) || jadwals[0];

                if (! isEdit || mengajarSelect.value !== originalMengajarId) {
                    pertemuanKeInput.value = selected ? selected.nextPertemuanKe : '';
                }

                if (jadwal) {
                    jamMulaiInput.value = jadwal.jam_mulai;
                    jamSelesaiInput.value = jadwal.jam_selesai;
                    jadwalInfo.textContent = `${jadwal.hari.charAt(0).toUpperCase() + jadwal.hari.slice(1)}, ${jadwal.jam_mulai} - ${jadwal.jam_selesai}${jadwal.ruangan ? ' | ' + jadwal.ruangan : ''}`;
                    jadwalInfo.classList.remove('alert-warning');
                    jadwalInfo.classList.add('alert-info');
                    return;
                }

                jamMulaiInput.value = '';
                jamSelesaiInput.value = '';
                jadwalInfo.textContent = 'Jadwal untuk mengajar ini belum tersedia. Jam pertemuan akan disimpan kosong.';
                jadwalInfo.classList.remove('alert-info');
                jadwalInfo.classList.add('alert-warning');
            }

            mengajarSelect.addEventListener('change', updateJadwalFields);
            tanggalInput.addEventListener('change', updateJadwalFields);
            updateJadwalFields();
        });
    </script>
@endpush
