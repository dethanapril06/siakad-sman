@php
    $roleLabel = 'Pegawai TU';
    $dashboardRoute = 'pegawai-tu.dashboard';
    $profileRoute = 'pegawai-tu.profile';
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'pegawai-tu.dashboard',
            'active' => 'pegawai-tu.dashboard',
            'icon' => 'bx-home-circle',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'pegawai-tu.profile',
            'active' => 'pegawai-tu.profile',
            'icon' => 'bx-user',
        ],
        [
            'type' => 'header',
            'label' => 'Tata Usaha',
        ],
        [
            'type' => 'dropdown',
            'label' => 'Periode Akademik',
            'icon' => 'bx-calendar-event',
            'children' => [
                [
                    'label' => 'Tahun Akademik',
                    'route' => 'pegawai-tu.master.tahun-akademik.index',
                    'active' => 'pegawai-tu.master.tahun-akademik.*',
                ],
                [
                    'label' => 'Semester',
                    'route' => 'pegawai-tu.master.semester.index',
                    'active' => 'pegawai-tu.master.semester.*',
                ],
            ],
        ],
        [
            'type' => 'dropdown',
            'label' => 'Referensi Sekolah',
            'icon' => 'bx-data',
            'children' => [
                [
                    'label' => 'Pengaturan Sekolah & Kop',
                    'route' => 'pegawai-tu.master.sekolah.edit',
                    'active' => 'pegawai-tu.master.sekolah.*',
                ],
                [
                    'label' => 'Jurusan',
                    'route' => 'pegawai-tu.master.jurusan.index',
                    'active' => 'pegawai-tu.master.jurusan.*',
                ],
                [
                    'label' => 'Ruangan',
                    'route' => 'pegawai-tu.master.ruangan.index',
                    'active' => 'pegawai-tu.master.ruangan.*',
                ],
                [
                    'label' => 'Mata Pelajaran',
                    'route' => 'pegawai-tu.master.mata-pelajaran.index',
                    'active' => 'pegawai-tu.master.mata-pelajaran.*',
                ],
                [
                    'label' => 'Kelas',
                    'route' => 'pegawai-tu.master.kelas.index',
                    'active' => 'pegawai-tu.master.kelas.*',
                ],
            ],
        ],
        [
            'type' => 'dropdown',
            'label' => 'Data Warga Sekolah',
            'icon' => 'bx-group',
            'children' => [
                [
                    'label' => 'Pegawai TU',
                    'route' => 'pegawai-tu.master.pegawai-tu.index',
                    'active' => 'pegawai-tu.master.pegawai-tu.*',
                ],
                [
                    'label' => 'Guru',
                    'route' => 'pegawai-tu.master.guru.index',
                    'active' => 'pegawai-tu.master.guru.*',
                ],
                [
                    'label' => 'Siswa',
                    'route' => 'pegawai-tu.master.siswa.index',
                    'active' => 'pegawai-tu.master.siswa.*',
                ],
            ],
        ],
        [
            'type' => 'dropdown',
            'label' => 'Akademik',
            'icon' => 'bx-book-open',
            'children' => [
                [
                    'label' => 'Kelas & Anggota',
                    'route' => 'pegawai-tu.akademik.kelas-akademik.index',
                    'active' => [
                        'pegawai-tu.akademik.kelas-akademik.*',
                        'pegawai-tu.akademik.anggota-kelas.*',
                    ],
                ],
                [
                    'label' => 'Penugasan Mengajar',
                    'route' => 'pegawai-tu.akademik.mengajar.index',
                    'active' => 'pegawai-tu.akademik.mengajar.*',
                ],
                [
                    'label' => 'Jadwal Pelajaran',
                    'route' => 'pegawai-tu.akademik.jadwal.index',
                    'active' => 'pegawai-tu.akademik.jadwal.*',
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
                    'route' => 'pegawai-tu.laporan.nilai.index',
                    'active' => 'pegawai-tu.laporan.nilai.*',
                ],
                [
                    'label' => 'Laporan Absensi',
                    'route' => 'pegawai-tu.laporan.absensi.index',
                    'active' => 'pegawai-tu.laporan.absensi.*',
                ],
                [
                    'label' => 'Laporan Keterlambatan',
                    'route' => 'pegawai-tu.laporan.keterlambatan.index',
                    'active' => 'pegawai-tu.laporan.keterlambatan.*',
                ],
                [
                    'label' => 'Jadwal Guru',
                    'route' => 'pegawai-tu.laporan.jadwal-guru.index',
                    'active' => 'pegawai-tu.laporan.jadwal-guru.*',
                ],
            ],
        ],
        [
            'type' => 'header',
            'label' => 'Manajemen Akun',
        ],
        [
            'label' => 'Data Pengguna',
            'route' => 'pegawai-tu.master.user.index',
            'active' => 'pegawai-tu.master.user.*',
            'icon' => 'bx-user-check',
        ],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
