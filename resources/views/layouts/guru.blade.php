@php
    $guru = auth()->user()->guru;
    $isWaliKelasAktif = $guru?->isWaliKelasAktif() ?? false;
    $isWaliKelasContext = request()->routeIs('wali-kelas.*');
    
    $roleLabel = $isWaliKelasContext 
        ? 'Wali Kelas ' . ($guru?->kelasWaliAktif()?->nama_lengkap ?? '')
        : ($isWaliKelasAktif ? 'Guru & Wali Kelas' : 'Guru Pengampu');
    $dashboardRoute = $isWaliKelasContext ? 'wali-kelas.dashboard' : 'guru.dashboard';
    $profileRoute = 'guru.profile';
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'guru.dashboard',
            'active' => 'guru.dashboard',
            'icon' => 'bx-home-circle',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'guru.profile',
            'active' => 'guru.profile',
            'icon' => 'bx-user',
        ],
        [
            'type' => 'header',
            'label' => 'Pembelajaran',
        ],
        [
            'label' => 'Mengajar',
            'route' => 'guru.mengajar.index',
            'active' => 'guru.mengajar.*',
            'icon' => 'bx-chalkboard',
        ],
        [
            'label' => 'Jadwal',
            'route' => 'guru.jadwal.index',
            'active' => 'guru.jadwal.*',
            'icon' => 'bx-calendar',
        ],
        [
            'label' => 'Pertemuan',
            'route' => 'guru.pertemuan.index',
            'active' => 'guru.pertemuan.*',
            'icon' => 'bx-book-content',
        ],
        [
            'label' => 'Absensi',
            'route' => 'guru.absensi.index',
            'active' => 'guru.absensi.*',
            'icon' => 'bx-check-square',
        ],
        [
            'label' => 'Penilaian',
            'route' => 'guru.penilaian.index',
            'active' => [
                'guru.penilaian.*',
                'guru.nilai.*',
            ],
            'icon' => 'bx-list-check',
        ],
        [
            'label' => 'Laporan Nilai',
            'route' => 'guru.laporan-nilai.index',
            'active' => 'guru.laporan-nilai.*',
            'icon' => 'bx-spreadsheet',
        ],
        [
            'label' => 'Laporan Absensi',
            'route' => 'guru.laporan-absensi.index',
            'active' => 'guru.laporan-absensi.*',
            'icon' => 'bx-calendar-check',
        ],
        [
            'label' => 'Laporan Keterlambatan',
            'route' => 'guru.laporan-keterlambatan.index',
            'active' => 'guru.laporan-keterlambatan.*',
            'icon' => 'bx-time-five',
        ],
    ];

    if ($isWaliKelasAktif) {
        $menus[] = [
            'type' => 'header',
            'label' => 'Wali Kelas',
        ];

        $menus[] = [
            'label' => 'Dashboard Wali',
            'route' => 'wali-kelas.dashboard',
            'active' => 'wali-kelas.dashboard',
            'icon' => 'bx-tachometer',
        ];

        $menus[] = [
            'label' => 'Siswa Wali',
            'route' => 'wali-kelas.siswa.index',
            'active' => 'wali-kelas.siswa.*',
            'icon' => 'bx-group',
        ];

        $menus[] = [
            'label' => 'Absensi Wali',
            'route' => 'wali-kelas.absensi.index',
            'active' => 'wali-kelas.absensi.*',
            'icon' => 'bx-calendar-check',
        ];

        $menus[] = [
            'label' => 'Nilai Wali',
            'route' => 'wali-kelas.nilai.index',
            'active' => 'wali-kelas.nilai.*',
            'icon' => 'bx-bar-chart-alt-2',
        ];

        $menus[] = [
            'label' => 'E-Rapor & Catatan',
            'route' => 'wali-kelas.rapor.index',
            'active' => 'wali-kelas.rapor.*',
            'icon' => 'bx-certification',
        ];
    }
@endphp

@include('layouts.partials.dashboard-shell')
