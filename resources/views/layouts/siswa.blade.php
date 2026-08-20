@php
    $roleLabel = 'Siswa';
    $dashboardRoute = 'siswa.dashboard';
    $profileRoute = 'siswa.profile';
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'siswa.dashboard',
            'active' => 'siswa.dashboard',
            'icon' => 'bx-home-circle',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'siswa.profile',
            'active' => 'siswa.profile',
            'icon' => 'bx-user',
        ],
        [
            'type' => 'header',
            'label' => 'Akademik Siswa',
        ],
        [
            'label' => 'Jadwal',
            'route' => 'siswa.jadwal.index',
            'active' => 'siswa.jadwal.*',
            'icon' => 'bx-calendar',
        ],
        [
            'label' => 'Nilai',
            'route' => 'siswa.nilai.index',
            'active' => 'siswa.nilai.*',
            'icon' => 'bx-bar-chart-alt-2',
        ],
        [
            'label' => 'Absensi',
            'route' => 'siswa.absensi.index',
            'active' => 'siswa.absensi.*',
            'icon' => 'bx-calendar-check',
        ],
        [
            'label' => 'Rapor Saya',
            'route' => 'siswa.rapor.index',
            'active' => 'siswa.rapor.*',
            'icon' => 'bx-certification',
        ],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
