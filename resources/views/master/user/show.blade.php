@extends('layouts.pegawai-tu')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master / <a href="{{ route('pegawai-tu.master.user.index') }}" class="text-muted">Data Pengguna</a> /</span>
            Detail Pengguna
        </h4>
    </div>

    @php
        $roleBadgeClasses = [
            'pegawai_tu' => 'bg-label-primary',
            'guru' => 'bg-label-info',
            'siswa' => 'bg-label-warning',
            'kepala_sekolah' => 'bg-label-danger',
        ];
        $roleDisplayNames = [
            'pegawai_tu' => 'Pegawai Tata Usaha',
            'guru' => 'Guru Mata Pelajaran',
            'siswa' => 'Siswa',
            'kepala_sekolah' => 'Kepala Sekolah',
        ];
        $roleName = $user->role?->name ?? 'unknown';
        $badgeClass = $roleBadgeClasses[$roleName] ?? 'bg-label-secondary';
        $displayName = $roleDisplayNames[$roleName] ?? ucwords(str_replace('_', ' ', $roleName));
    @endphp

    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Akun</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pegawai-tu.master.user.edit', $user) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a href="{{ route('pegawai-tu.master.user.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary fs-3 font-semibold">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <span class="badge {{ $badgeClass }} mt-1">{{ $displayName }}</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="text-muted">Nama Lengkap</th>
                                    <td>: {{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email</th>
                                    <td>: {{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Hak Akses (Role)</th>
                                    <td>: <span class="badge {{ $badgeClass }}">{{ $displayName }}</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Status Akun</th>
                                    <td>:
                                        @if ($user->is_active)
                                            <span class="badge bg-label-success">Aktif</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Terdaftar Pada</th>
                                    <td>: {{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Pembaruan Terakhir</th>
                                    <td>: {{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') : '-' }}</td>
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
                    <h5 class="mb-0">Profil Warga Sekolah Terkait</h5>
                </div>
                <div class="card-body">
                    @if ($user->pegawaiTu)
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <i class="bx bx-id-card fs-4 me-2"></i>
                            <div>Profil Pegawai Tata Usaha Terhubung</div>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="text-muted">NIP</th>
                                    <td>: {{ $user->pegawaiTu->nip ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nama Pegawai</th>
                                    <td>: {{ $user->pegawaiTu->nama }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jenis Kelamin</th>
                                    <td>: {{ $user->pegawaiTu->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">No. HP</th>
                                    <td>: {{ $user->pegawaiTu->no_hp ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Alamat</th>
                                    <td>: {{ $user->pegawaiTu->alamat ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @elseif ($user->guru)
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bx bx-user-pin fs-4 me-2"></i>
                            <div>Profil Guru / Kepala Sekolah Terhubung</div>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="text-muted">NIP</th>
                                    <td>: {{ $user->guru->nip }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nama Guru</th>
                                    <td>: {{ $user->guru->nama }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Status Guru</th>
                                    <td>:
                                        @if ($user->guru->status === 'aktif')
                                            <span class="badge bg-label-success">Aktif</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">No. HP</th>
                                    <td>: {{ $user->guru->no_hp ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Alamat</th>
                                    <td>: {{ $user->guru->alamat ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <a href="{{ route('pegawai-tu.master.guru.show', $user->guru) }}" class="btn btn-sm btn-outline-info">
                                <i class="bx bx-link-external me-1"></i> Buka Master Guru
                            </a>
                        </div>
                    @elseif ($user->siswa)
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bx bx-face fs-4 me-2"></i>
                            <div>Profil Siswa Terhubung</div>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="text-muted">NIS / NISN</th>
                                    <td>: {{ $user->siswa->nis }} / {{ $user->siswa->nisn ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nama Siswa</th>
                                    <td>: {{ $user->siswa->nama }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Status Siswa</th>
                                    <td>:
                                        @if ($user->siswa->status === 'aktif')
                                            <span class="badge bg-label-success">Aktif</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ ucfirst($user->siswa->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">No. HP Siswa</th>
                                    <td>: {{ $user->siswa->no_hp ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nama Ayah / Ibu</th>
                                    <td>: {{ $user->siswa->nama_ayah ?: '-' }} / {{ $user->siswa->nama_ibu ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <a href="{{ route('pegawai-tu.master.siswa.show', $user->siswa) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bx bx-link-external me-1"></i> Buka Master Siswa
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-info-circle fs-1 d-block mb-2"></i>
                            Belum ada entri profil spesifik yang terhubung langsung.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
