@php
    $roleLabel = 'Kepala Sekolah';
    $dashboardRoute = 'kepala-sekolah.dashboard';
    $profileRoute = 'kepala-sekolah.profile';
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'kepala-sekolah.dashboard',
            'active' => 'kepala-sekolah.dashboard',
            'icon' => 'bx-home-circle',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'kepala-sekolah.profile',
            'active' => 'kepala-sekolah.profile',
            'icon' => 'bx-user',
        ],
        [
            'type' => 'header',
            'label' => 'Monitoring',
        ],
        [
            'type' => 'dropdown',
            'label' => 'Master Data',
            'icon' => 'bx-data',
            'children' => [
                [
                    'label' => 'Pengaturan Sekolah & Kop',
                    'route' => 'kepala-sekolah.master.sekolah.edit',
                    'active' => 'kepala-sekolah.master.sekolah.*',
                ],
                [
                    'label' => 'Tahun Akademik',
                    'route' => 'kepala-sekolah.master.tahun-akademik.index',
                    'active' => 'kepala-sekolah.master.tahun-akademik.*',
                ],
                [
                    'label' => 'Semester',
                    'route' => 'kepala-sekolah.master.semester.index',
                    'active' => 'kepala-sekolah.master.semester.*',
                ],
                [
                    'label' => 'Jurusan',
                    'route' => 'kepala-sekolah.master.jurusan.index',
                    'active' => 'kepala-sekolah.master.jurusan.*',
                ],
                [
                    'label' => 'Ruangan',
                    'route' => 'kepala-sekolah.master.ruangan.index',
                    'active' => 'kepala-sekolah.master.ruangan.*',
                ],
                [
                    'label' => 'Mata Pelajaran',
                    'route' => 'kepala-sekolah.master.mata-pelajaran.index',
                    'active' => 'kepala-sekolah.master.mata-pelajaran.*',
                ],
                [
                    'label' => 'Guru',
                    'route' => 'kepala-sekolah.master.guru.index',
                    'active' => 'kepala-sekolah.master.guru.*',
                ],
                [
                    'label' => 'Siswa',
                    'route' => 'kepala-sekolah.master.siswa.index',
                    'active' => 'kepala-sekolah.master.siswa.*',
                ],
                [
                    'label' => 'Kelas',
                    'route' => 'kepala-sekolah.master.kelas.index',
                    'active' => 'kepala-sekolah.master.kelas.*',
                ],
            ],
        ],
        [
            'type' => 'dropdown',
            'label' => 'Akademik',
            'icon' => 'bx-book-open',
            'children' => [
                [
                    'label' => 'Kelas Akademik',
                    'route' => 'kepala-sekolah.akademik.kelas-akademik.index',
                    'active' => 'kepala-sekolah.akademik.kelas-akademik.*',
                ],
                [
                    'label' => 'Mengajar',
                    'route' => 'kepala-sekolah.akademik.mengajar.index',
                    'active' => 'kepala-sekolah.akademik.mengajar.*',
                ],
                [
                    'label' => 'Jadwal',
                    'route' => 'kepala-sekolah.akademik.jadwal.index',
                    'active' => 'kepala-sekolah.akademik.jadwal.*',
                ],
            ],
        ],
        [
            'type' => 'dropdown',
            'label' => 'Laporan',
            'icon' => 'bx-file',
            'children' => [
                [
                    'label' => 'Laporan Nilai',
                    'route' => 'kepala-sekolah.laporan.nilai.index',
                    'active' => 'kepala-sekolah.laporan.nilai.*',
                ],
                [
                    'label' => 'Laporan Absensi',
                    'route' => 'kepala-sekolah.laporan.absensi.index',
                    'active' => 'kepala-sekolah.laporan.absensi.*',
                ],
                [
                    'label' => 'Laporan Keterlambatan',
                    'route' => 'kepala-sekolah.laporan.keterlambatan.index',
                    'active' => 'kepala-sekolah.laporan.keterlambatan.*',
                ],
                [
                    'label' => 'Jadwal Guru',
                    'route' => 'kepala-sekolah.laporan.jadwal-guru.index',
                    'active' => 'kepala-sekolah.laporan.jadwal-guru.*',
                ],
            ],
        ],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
