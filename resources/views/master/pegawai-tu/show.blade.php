@extends('layouts.pegawai-tu')

@section('title', 'Detail Pegawai TU')

@section('content')
    <div class="py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master / <a href="{{ route('pegawai-tu.master.pegawai-tu.index') }}" class="text-muted">Pegawai TU</a> /</span>
            Detail Pegawai TU
        </h4>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Pribadi</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pegawai-tu.master.pegawai-tu.edit', $pegawaiTu) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a href="{{ route('pegawai-tu.master.pegawai-tu.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary fs-3 font-semibold">
                                {{ strtoupper(substr($pegawaiTu->nama, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $pegawaiTu->nama }}</h5>
                            <span class="badge bg-label-primary mt-1">Pegawai Tata Usaha</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="text-muted">NIP</th>
                                    <td>: <strong>{{ $pegawaiTu->nip ?: '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nama Lengkap</th>
                                    <td>: {{ $pegawaiTu->nama }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jenis Kelamin</th>
                                    <td>: {{ $pegawaiTu->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tempat, Tgl Lahir</th>
                                    <td>: {{ $pegawaiTu->tempat_lahir ?: '-' }}, {{ $pegawaiTu->tanggal_lahir ? $pegawaiTu->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">No. HP</th>
                                    <td>: {{ $pegawaiTu->no_hp ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Alamat</th>
                                    <td>: {{ $pegawaiTu->alamat ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Akun Login</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <i class="bx bx-shield-quarter fs-4 me-2"></i>
                        <div>Kredensial Login Sistem</div>
                    </div>

                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 35%;" class="text-muted">Email</th>
                                <td>: {{ $pegawaiTu->user?->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Hak Akses</th>
                                <td>: <span class="badge bg-label-primary">Pegawai TU</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status Akun</th>
                                <td>:
                                    @if ($pegawaiTu->user?->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dibuat Pada</th>
                                <td>: {{ $pegawaiTu->created_at ? $pegawaiTu->created_at->translatedFormat('d F Y, H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Pembaruan Terakhir</th>
                                <td>: {{ $pegawaiTu->updated_at ? $pegawaiTu->updated_at->translatedFormat('d F Y, H:i') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
