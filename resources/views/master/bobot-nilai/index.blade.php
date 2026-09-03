@php
    $isPegawaiTu = auth()->user()->isPegawaiTu();
    $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';
    $routePrefix = $isPegawaiTu ? 'pegawai-tu.master.bobot-nilai' : 'kepala-sekolah.master.bobot-nilai';
@endphp

@extends($layout)

@section('title', 'Bobot Nilai')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master /</span> Pengaturan Bobot Nilai</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1">Komponen & Persentase Bobot Nilai</h5>
                <small class="text-muted">Atur persentase bobot tiap kategori penilaian. Total persentase seluruh komponen aktif harus tepat 100%.</small>
            </div>
            <div id="badge-total-container">
                <span class="badge {{ $totalBobot === 100 ? 'bg-label-success' : 'bg-label-danger' }} fs-6" id="badge-total">
                    Total Bobot: <span id="total-val">{{ $totalBobot }}</span>%
                </span>
            </div>
        </div>

        @if ($isPegawaiTu)
            <form action="{{ route('pegawai-tu.master.bobot-nilai.update') }}" method="POST">
                @csrf
                @method('PUT')
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Kategori Penilaian</th>
                        <th style="width: 200px;">Persentase Bobot (%)</th>
                        <th style="width: 140px;">Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jenisNilais as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-label-primary font-monospace">{{ $item->kode }}</span></td>
                            <td>
                                <strong>{{ $item->nama }}</strong>
                            </td>
                            <td>
                                @if ($isPegawaiTu)
                                    <div class="input-group input-group-merge" style="max-width: 140px;">
                                        <input
                                            type="number"
                                            name="bobot[{{ $item->id }}]"
                                            id="bobot_{{ $item->id }}"
                                            class="form-control bobot-input @error('bobot.' . $item->id) is-invalid @enderror"
                                            value="{{ old('bobot.' . $item->id, $item->bobot) }}"
                                            min="0"
                                            max="100"
                                            step="1"
                                            data-active="{{ $item->is_active ? '1' : '0' }}"
                                            required
                                        />
                                        <span class="input-group-text">%</span>
                                    </div>
                                @else
                                    <span class="fw-bold">{{ $item->bobot }}%</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Data jenis nilai belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($isPegawaiTu)
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    <i class="bx bx-info-circle me-1"></i> Perubahan bobot ini akan langsung diterapkan pada laporan nilai guru, ledger wali kelas, dan cetak rapor.
                </div>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="bx bx-save me-1"></i> Simpan Pengaturan Bobot
                </button>
            </div>
            </form>
        @endif
    </div>

    @if ($isPegawaiTu)
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.bobot-input');
        const totalVal = document.getElementById('total-val');
        const badgeTotal = document.getElementById('badge-total');
        const btnSubmit = document.getElementById('btn-submit');

        function updateTotal() {
            let sum = 0;
            inputs.forEach(input => {
                if (input.getAttribute('data-active') === '1') {
                    sum += parseInt(input.value || 0, 10);
                }
            });

            totalVal.textContent = sum;

            if (sum === 100) {
                badgeTotal.className = 'badge bg-label-success fs-6';
                badgeTotal.innerHTML = `Total Bobot: ${sum}% <i class="bx bx-check"></i>`;
            } else {
                badgeTotal.className = 'badge bg-label-danger fs-6';
                badgeTotal.innerHTML = `Total Bobot: ${sum}% (Wajib 100%)`;
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', updateTotal);
        });

        updateTotal();
    });
    </script>
    @endif
@endsection
