@php
    $mengajar ??= null;
    $selectedSemesterId = old('semester_id', $mengajar?->semester_id ?? $activeSemesterId ?? null);
    $selectedKelasId = old('kelas_akademik_id', $mengajar?->kelas_akademik_id ?? null);
    $selectedMapelId = old('mata_pelajaran_id', $mengajar?->mata_pelajaran_id ?? null);
    $selectedGuruId = old('guru_id', $mengajar?->guru_id ?? null);
@endphp

<div class="mb-3">
    <label class="form-label" for="semester_id">Semester <span class="text-danger">*</span></label>
    <select class="form-select @error('semester_id') is-invalid @enderror" id="semester_id" name="semester_id" autofocus>
        <option value="">Pilih semester</option>
        @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}"
                data-tahun-akademik-id="{{ $semester->tahun_akademik_id }}"
                @selected((string) $selectedSemesterId === (string) $semester->id)>
                {{ $semester->nama_lengkap }}
            </option>
        @endforeach
    </select>
    @error('semester_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="kelas_akademik_id">Kelas Akademik <span class="text-danger">*</span></label>
    <select class="form-select @error('kelas_akademik_id') is-invalid @enderror" id="kelas_akademik_id" name="kelas_akademik_id">
        <option value="">Pilih kelas akademik</option>
        @foreach ($kelasAkademiks as $kelasAkademik)
            <option value="{{ $kelasAkademik->id }}"
                data-tahun-akademik-id="{{ $kelasAkademik->tahun_akademik_id }}"
                @selected((string) $selectedKelasId === (string) $kelasAkademik->id)>
                {{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama }}
            </option>
        @endforeach
    </select>
    <div id="kelas-help" class="form-text text-muted">Hanya menampilkan kelas yang sesuai dengan Tahun Akademik semester yang dipilih.</div>
    @error('kelas_akademik_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="guru_id">Guru Pengajar <span class="text-danger">*</span></label>
    <select class="form-select @error('guru_id') is-invalid @enderror" id="guru_id" name="guru_id">
        <option value="">Pilih guru</option>
        @foreach ($gurus as $guru)
            <option value="{{ $guru->id }}" @selected((string) $selectedGuruId === (string) $guru->id)>
                {{ $guru->nama }}{{ $guru->nip ? ' - ' . $guru->nip : '' }}
            </option>
        @endforeach
    </select>
    @error('guru_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="mata_pelajaran_id">Mata Pelajaran <span class="text-danger">*</span></label>
    <select class="form-select @error('mata_pelajaran_id') is-invalid @enderror" id="mata_pelajaran_id" name="mata_pelajaran_id">
        <option value="">Pilih mata pelajaran</option>
        @foreach ($mataPelajarans as $mataPelajaran)
            <option value="{{ $mataPelajaran->id }}"
                data-base-label="{{ $mataPelajaran->nama }}{{ $mataPelajaran->kode ? ' (' . $mataPelajaran->kode . ')' : '' }}"
                @selected((string) $selectedMapelId === (string) $mataPelajaran->id)>
                {{ $mataPelajaran->nama }}{{ $mataPelajaran->kode ? ' (' . $mataPelajaran->kode . ')' : '' }}
            </option>
        @endforeach
    </select>
    <div id="mapel-alert" class="alert alert-warning d-none py-2 px-3 mt-2 mb-0" role="alert">
        <i class="bx bx-info-circle me-1"></i> Semua mata pelajaran pada kelas ini sudah ditugaskan kepada guru untuk semester yang dipilih.
    </div>
    @error('mata_pelajaran_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const existingPenugasan = @json($existingPenugasan ?? []);
    const currentMengajarId = @json($mengajar?->id ?? null);

    const semesterSelect = document.getElementById('semester_id');
    const kelasSelect = document.getElementById('kelas_akademik_id');
    const mapelSelect = document.getElementById('mata_pelajaran_id');
    const mapelAlert = document.getElementById('mapel-alert');

    function filterKelasOptions() {
        const selectedSemesterOpt = semesterSelect.options[semesterSelect.selectedIndex];
        const tahunAkademikId = selectedSemesterOpt ? selectedSemesterOpt.getAttribute('data-tahun-akademik-id') : null;

        let selectedKelasStillValid = false;

        Array.from(kelasSelect.options).forEach((opt, index) => {
            if (index === 0) return; // Keep placeholder option

            const optTahunId = opt.getAttribute('data-tahun-akademik-id');
            if (!tahunAkademikId || optTahunId === tahunAkademikId) {
                opt.hidden = false;
                opt.disabled = false;
                if (opt.value === kelasSelect.value) {
                    selectedKelasStillValid = true;
                }
            } else {
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        if (!selectedKelasStillValid && kelasSelect.value !== '') {
            kelasSelect.value = '';
        }

        updateMapelAvailability();
    }

    function updateMapelAvailability() {
        const semesterId = parseInt(semesterSelect.value, 10);
        const kelasId = parseInt(kelasSelect.value, 10);

        if (!semesterId || !kelasId) {
            // Show and enable all mapel
            Array.from(mapelSelect.options).forEach((opt, index) => {
                if (index === 0) return;
                opt.text = opt.getAttribute('data-base-label') || opt.text;
                opt.hidden = false;
                opt.disabled = false;
            });
            if (mapelAlert) mapelAlert.classList.add('d-none');
            return;
        }

        // Find existing assignments for this semester and class
        const assignedMap = {};
        existingPenugasan.forEach(item => {
            if (parseInt(item.semester_id, 10) === semesterId && parseInt(item.kelas_akademik_id, 10) === kelasId) {
                if (!currentMengajarId || parseInt(item.id, 10) !== parseInt(currentMengajarId, 10)) {
                    assignedMap[item.mata_pelajaran_id] = true;
                }
            }
        });

        let availableCount = 0;
        let totalMapelCount = 0;

        Array.from(mapelSelect.options).forEach((opt, index) => {
            if (index === 0) return;
            totalMapelCount++;
            const baseLabel = opt.getAttribute('data-base-label') || opt.text;
            const mapelId = parseInt(opt.value, 10);

            if (assignedMap[mapelId]) {
                opt.hidden = true;
                opt.disabled = true;
                if (mapelSelect.value === opt.value) {
                    mapelSelect.value = '';
                }
            } else {
                opt.text = baseLabel;
                opt.hidden = false;
                opt.disabled = false;
                availableCount++;
            }
        });

        if (totalMapelCount > 0 && availableCount === 0) {
            if (mapelAlert) mapelAlert.classList.remove('d-none');
        } else {
            if (mapelAlert) mapelAlert.classList.add('d-none');
        }
    }

    semesterSelect.addEventListener('change', filterKelasOptions);
    kelasSelect.addEventListener('change', updateMapelAvailability);

    // Initial run
    filterKelasOptions();
});
</script>
