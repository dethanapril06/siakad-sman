@php
    $layout = 'layouts.pegawai-tu';
    $routePrefix = 'pegawai-tu.master.user';
@endphp

@extends($layout)

@section('title', 'Data Pengguna & Akun')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Manajemen Akun /</span> Data Pengguna</h4>

        <a href="{{ route('pegawai-tu.master.user.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>
            Tambah Pengguna
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1">Daftar Seluruh Pengguna & Akun Login</h5>
                <small class="text-muted">Kelola akun, hak akses (role), status login, dan reset kata sandi seluruh warga sekolah.</small>
            </div>

            <form action="{{ route($routePrefix . '.index') }}" method="GET"
                class="d-flex flex-wrap align-items-center gap-2">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="width: auto; min-width: 200px;"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau email..."
                />

                <select name="role_id" class="form-select" style="width: auto;">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        @php
                            $roleLabels = [
                                'pegawai_tu' => 'Pegawai TU',
                                'guru' => 'Guru',
                                'siswa' => 'Siswa',
                                'kepala_sekolah' => 'Kepala Sekolah',
                            ];
                            $label = $roleLabels[$role->name] ?? ucwords(str_replace('_', ' ', $role->name));
                        @endphp
                        <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select name="is_active" class="form-select" style="width: auto;">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                </select>

                <button type="submit" class="btn btn-outline-primary" title="Cari">
                    <i class="bx bx-search"></i>
                </button>
                @if (request()->hasAny(['search', 'role_id', 'is_active']))
                    <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bx bx-refresh"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Profil Terhubung</th>
                        <th>Status Akun</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if (auth()->id() === $user->id)
                                            <span class="badge bg-label-info ms-1">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleBadgeClasses = [
                                        'pegawai_tu' => 'bg-label-primary',
                                        'guru' => 'bg-label-info',
                                        'siswa' => 'bg-label-warning',
                                        'kepala_sekolah' => 'bg-label-danger',
                                    ];
                                    $roleDisplayNames = [
                                        'pegawai_tu' => 'Pegawai TU',
                                        'guru' => 'Guru',
                                        'siswa' => 'Siswa',
                                        'kepala_sekolah' => 'Kepala Sekolah',
                                    ];
                                    $roleName = $user->role?->name ?? 'unknown';
                                    $badgeClass = $roleBadgeClasses[$roleName] ?? 'bg-label-secondary';
                                    $displayName = $roleDisplayNames[$roleName] ?? ucwords(str_replace('_', ' ', $roleName));
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $displayName }}</span>
                            </td>
                            <td>
                                @if ($user->pegawaiTu)
                                    <small class="text-muted d-block">TU (NIP: {{ $user->pegawaiTu->nip ?: '-' }})</small>
                                @elseif ($user->guru)
                                    <small class="text-muted d-block">Guru (NIP: {{ $user->guru->nip ?: '-' }})</small>
                                @elseif ($user->siswa)
                                    <small class="text-muted d-block">Siswa (NIS: {{ $user->siswa->nis ?: '-' }})</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-label-success me-1">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route($routePrefix . '.show', $user) }}">
                                            <i class="bx bx-show me-1"></i>
                                            Detail
                                        </a>

                                        <a class="dropdown-item" href="{{ route($routePrefix . '.edit', $user) }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                            Edit
                                        </a>

                                        <form action="{{ route('pegawai-tu.master.user.reset-password', $user) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item"
                                                onclick="return confirm('Reset password akun pengguna {{ $user->name }} menjadi default (password)?')">
                                                <i class="bx bx-key me-1"></i>
                                                Reset Password
                                            </button>
                                        </form>

                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route($routePrefix . '.destroy', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini? Seluruh data terkait akan terhapus.')">
                                                    <i class="bx bx-trash me-1"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3 d-flex justify-content-end">
            <x-pagination :paginator="$users" align="end" />
        </div>
    </div>
@endsection
