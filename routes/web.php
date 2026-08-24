<?php

use App\Http\Controllers\Akademik\AnggotaKelasController;
use App\Http\Controllers\Akademik\JadwalController;
use App\Http\Controllers\Akademik\KelasAkademikController;
use App\Http\Controllers\Akademik\MengajarController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\GuruController;
use App\Http\Controllers\Master\JurusanController;
use App\Http\Controllers\Master\KelasController;
use App\Http\Controllers\Master\MataPelajaranController;
use App\Http\Controllers\Master\PegawaiTuController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\Master\SiswaController;
use App\Http\Controllers\Master\TahunAkademikController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guru
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\JadwalController as GuruJadwalController;
use App\Http\Controllers\Guru\LaporanNilaiController as GuruLaporanNilaiController;
use App\Http\Controllers\Guru\MengajarController as GuruMengajarController;
use App\Http\Controllers\Guru\NilaiController as GuruNilaiController;
use App\Http\Controllers\Guru\PenilaianController as GuruPenilaianController;
use App\Http\Controllers\Guru\PertemuanController as GuruPertemuanController;

// Wali Kelas
use App\Http\Controllers\WaliKelas\AbsensiController as WaliKelasAbsensiController;
use App\Http\Controllers\WaliKelas\NilaiController as WaliKelasNilaiController;
use App\Http\Controllers\WaliKelas\RaporController as WaliKelasRaporController;
use App\Http\Controllers\WaliKelas\SiswaController as WaliKelasSiswaController;

// Siswa
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\JadwalController as SiswaJadwalController;
use App\Http\Controllers\Siswa\NilaiController as SiswaNilaiController;
use App\Http\Controllers\Siswa\RaporController as SiswaRaporController;

// Laporan
use App\Http\Controllers\Laporan\AbsensiController as LaporanAbsensiController;
use App\Http\Controllers\Laporan\JadwalGuruController as LaporanJadwalGuruController;
use App\Http\Controllers\Laporan\KeterlambatanController as LaporanKeterlambatanController;
use App\Http\Controllers\Laporan\NilaiController as LaporanNilaiController;

Route::get('/', [
    AuthController::class,
    'redirectAuthenticated',
])->name('home');

/*
|--------------------------------------------------------------------------
| Guest
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.process');
});

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Pegawai TU
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:pegawai_tu')
    ->prefix('pegawai-tu')
    ->name('pegawai-tu.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'pegawaiTu',
        ])->name('dashboard');
        Route::get('/profile', [
                ProfileController::class,
                'pegawaiTu',
            ])->name('profile');

        Route::prefix('master')
            ->name('master.')
            ->group(function () {
                Route::resource(
                    'tahun-akademik',
                    TahunAkademikController::class
                );

                Route::resource(
                    'semester',
                    SemesterController::class
                );
                Route::put(
                    'semester/{semester}/toggle-rapor',
                    [SemesterController::class, 'toggleRapor']
                )->name('semester.toggle-rapor');

                Route::resource(
                    'jurusan',
                    JurusanController::class
                );

                Route::resource(
                    'ruangan',
                    RuanganController::class
                );

                Route::resource(
                    'mata-pelajaran',
                    MataPelajaranController::class
                );

                Route::resource(
                    'pegawai-tu',
                    PegawaiTuController::class
                );
                Route::put(
                    'pegawai-tu/{pegawai_tu}/reset-password',
                    [PegawaiTuController::class, 'resetPassword']
                )->name('pegawai-tu.reset-password');

                Route::resource(
                    'guru',
                    GuruController::class
                );
                Route::put(
                    'guru/{guru}/reset-password',
                    [GuruController::class, 'resetPassword']
                )->name('guru.reset-password');

                Route::resource(
                    'siswa',
                    SiswaController::class
                );
                Route::put(
                    'siswa/{siswa}/reset-password',
                    [SiswaController::class, 'resetPassword']
                )->name('siswa.reset-password');

                Route::resource(
                    'kelas',
                    KelasController::class
                )->parameters([
                    'kelas' => 'kelas',
                ]);

                Route::resource(
                    'user',
                    UserController::class
                );
                Route::put(
                    'user/{user}/reset-password',
                    [UserController::class, 'resetPassword']
                )->name('user.reset-password');
            });
        
        Route::prefix('akademik')
            ->name('akademik.')
            ->group(function () {
                Route::resource(
                    'kelas-akademik',
                    KelasAkademikController::class
                );

                Route::get(
                    'kelas-akademik/{kelasAkademik}/anggota',
                    [
                        AnggotaKelasController::class,
                        'index',
                    ]
                )->name('anggota-kelas.index');

                Route::get(
                    'kelas-akademik/{kelasAkademik}/anggota/create',
                    [
                        AnggotaKelasController::class,
                        'create',
                    ]
                )->name('anggota-kelas.create');

                Route::post(
                    'kelas-akademik/{kelasAkademik}/anggota',
                    [
                        AnggotaKelasController::class,
                        'store',
                    ]
                )->name('anggota-kelas.store');

                Route::get(
                    'anggota-kelas/{anggotaKelas}/pindah',
                    [
                        AnggotaKelasController::class,
                        'pindahForm',
                    ]
                )->name('anggota-kelas.pindah-form');

                Route::put(
                    'anggota-kelas/{anggotaKelas}/pindah',
                    [
                        AnggotaKelasController::class,
                        'pindah',
                    ]
                )->name('anggota-kelas.pindah');

                Route::delete(
                    'anggota-kelas/{anggotaKelas}',
                    [
                        AnggotaKelasController::class,
                        'destroy',
                    ]
                )->name('anggota-kelas.destroy');

                Route::resource(
                    'mengajar',
                    MengajarController::class
                );

                Route::resource(
                    'jadwal',
                    JadwalController::class
                );
            });
        
        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {
                Route::get('/nilai', [
                    LaporanNilaiController::class,
                    'index',
                ])->name('nilai.index');
                Route::get('/nilai/cetak', [
                    LaporanNilaiController::class,
                    'cetak',
                ])->name('nilai.cetak');
                Route::get('/nilai/export', [
                    LaporanNilaiController::class,
                    'export',
                ])->name('nilai.export');

                Route::get('/absensi', [
                    LaporanAbsensiController::class,
                    'index',
                ])->name('absensi.index');
                Route::get('/absensi/cetak', [
                    LaporanAbsensiController::class,
                    'cetak',
                ])->name('absensi.cetak');
                Route::get('/absensi/export', [
                    LaporanAbsensiController::class,
                    'export',
                ])->name('absensi.export');

                Route::get('/keterlambatan', [
                    LaporanKeterlambatanController::class,
                    'index',
                ])->name('keterlambatan.index');
                Route::get('/keterlambatan/cetak', [
                    LaporanKeterlambatanController::class,
                    'cetak',
                ])->name('keterlambatan.cetak');
                Route::get('/keterlambatan/export', [
                    LaporanKeterlambatanController::class,
                    'export',
                ])->name('keterlambatan.export');

                Route::get('/jadwal-guru', [
                    LaporanJadwalGuruController::class,
                    'index',
                ])->name('jadwal-guru.index');
                Route::get('/jadwal-guru/cetak', [
                    LaporanJadwalGuruController::class,
                    'cetak',
                ])->name('jadwal-guru.cetak');
                Route::get('/jadwal-guru/export', [
                    LaporanJadwalGuruController::class,
                    'export',
                ])->name('jadwal-guru.export');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | Guru
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:guru')
        ->prefix('guru')
        ->name('guru.')
        ->group(function () {
            Route::get('/dashboard', [
                DashboardController::class,
                'guru',
            ])->name('dashboard');

            Route::get('/profile', [
                ProfileController::class,
                'guru',
            ])->name('profile');

            Route::get('/mengajar', [
                GuruMengajarController::class,
                'index',
            ])->name('mengajar.index');

            Route::get('/mengajar/{mengajar}', [
                GuruMengajarController::class,
                'show',
            ])->name('mengajar.show');

            Route::get('/jadwal', [
                GuruJadwalController::class,
                'index',
            ])->name('jadwal.index');

            Route::resource(
                'pertemuan',
                GuruPertemuanController::class
            );

            Route::get('/absensi', [
                GuruAbsensiController::class,
                'index',
            ])->name('absensi.index');

            Route::get(
                '/absensi/{pertemuan}/edit',
                [
                    GuruAbsensiController::class,
                    'edit',
                ]
            )->name('absensi.edit');

            Route::put(
                '/absensi/{pertemuan}',
                [
                    GuruAbsensiController::class,
                    'update',
                ]
            )->name('absensi.update');

            Route::resource(
                'penilaian',
                GuruPenilaianController::class
            );

            Route::get(
                '/penilaian/{penilaian}/nilai/edit',
                [
                    GuruNilaiController::class,
                    'edit',
                ]
            )->name('nilai.edit');

            Route::put(
                '/penilaian/{penilaian}/nilai',
                [
                    GuruNilaiController::class,
                    'update',
                ]
            )->name('nilai.update');

            Route::get('/laporan-nilai', [
                GuruLaporanNilaiController::class,
                'index',
            ])->name('laporan-nilai.index');
        });
    
    Route::middleware([
        'role:guru',
        'wali.kelas',
    ])
        ->prefix('wali-kelas')
        ->name('wali-kelas.')
        ->group(function () {
            Route::get('/dashboard', [
                DashboardController::class,
                'waliKelas',
            ])->name('dashboard');

            Route::get('/siswa', [
                WaliKelasSiswaController::class,
                'index',
            ])->name('siswa.index');

            Route::get('/siswa/{siswa}', [
                WaliKelasSiswaController::class,
                'show',
            ])->name('siswa.show');


            Route::get('/nilai', [
                WaliKelasNilaiController::class,
                'index',
            ])->name('nilai.index');

            Route::get('/nilai/siswa/{siswa}', [
                WaliKelasNilaiController::class,
                'show',
            ])->name('nilai.show');


            Route::get('/absensi', [
                WaliKelasAbsensiController::class,
                'index',
            ])->name('absensi.index');

            Route::get('/absensi/siswa/{siswa}', [
                WaliKelasAbsensiController::class,
                'show',
            ])->name('absensi.show');

            Route::get('/rapor', [
                WaliKelasRaporController::class,
                'index',
            ])->name('rapor.index');

            Route::put('/rapor/siswa/{siswa}/catatan', [
                WaliKelasRaporController::class,
                'updateCatatan',
            ])->name('rapor.catatan');

            Route::get('/rapor/siswa/{siswa}/cetak', [
                WaliKelasRaporController::class,
                'cetakSiswa',
            ])->name('rapor.cetak');
        });

    /*
    |--------------------------------------------------------------------------
    | Siswa
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:siswa')
        ->prefix('siswa')
        ->name('siswa.')
        ->group(function () {
            Route::get('/dashboard', [
                DashboardController::class,
                'siswa',
            ])->name('dashboard');

            Route::get('/jadwal', [
                SiswaJadwalController::class,
                'index',
            ])->name('jadwal.index');

            Route::get('/nilai', [
                SiswaNilaiController::class,
                'index',
            ])->name('nilai.index');

            Route::get('/absensi', [
                SiswaAbsensiController::class,
                'index',
            ])->name('absensi.index');

            Route::get('/rapor', [
                SiswaRaporController::class,
                'index',
            ])->name('rapor.index');

            Route::get('/rapor/cetak', [
                SiswaRaporController::class,
                'cetak',
            ])->name('rapor.cetak');

            Route::get('/profile', [
                ProfileController::class,
                'siswa',
            ])->name('profile');
        });

    /*
    |--------------------------------------------------------------------------
    | Kepala Sekolah
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:kepala_sekolah')
        ->prefix('kepala-sekolah')
        ->name('kepala-sekolah.')
        ->group(function () {
            Route::get('/dashboard', [
                DashboardController::class,
                'kepalaSekolah',
            ])->name('dashboard');

            Route::get('/profile', [
                ProfileController::class,
                'kepalaSekolah',
            ])->name('profile');

            Route::prefix('master')
                ->name('master.')
                ->group(function () {
                    Route::resource(
                        'tahun-akademik',
                        TahunAkademikController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'semester',
                        SemesterController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'jurusan',
                        JurusanController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'ruangan',
                        RuanganController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'mata-pelajaran',
                        MataPelajaranController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'guru',
                        GuruController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'siswa',
                        SiswaController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'kelas',
                        KelasController::class
                    )->parameters([
                        'kelas' => 'kelas',
                    ])->only([
                        'index',
                        'show',
                    ]);
                });

            Route::prefix('akademik')
                ->name('akademik.')
                ->group(function () {
                    Route::resource(
                        'kelas-akademik',
                        KelasAkademikController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'mengajar',
                        MengajarController::class
                    )->only([
                        'index',
                        'show',
                    ]);

                    Route::resource(
                        'jadwal',
                        JadwalController::class
                    )->only([
                        'index',
                        'show',
                    ]);
                });

            Route::prefix('laporan')
                ->name('laporan.')
                ->group(function () {
                    Route::get('/nilai', [
                        LaporanNilaiController::class,
                        'index',
                    ])->name('nilai.index');
                    Route::get('/nilai/cetak', [
                        LaporanNilaiController::class,
                        'cetak',
                    ])->name('nilai.cetak');
                    Route::get('/nilai/export', [
                        LaporanNilaiController::class,
                        'export',
                    ])->name('nilai.export');

                    Route::get('/absensi', [
                        LaporanAbsensiController::class,
                        'index',
                    ])->name('absensi.index');
                    Route::get('/absensi/cetak', [
                        LaporanAbsensiController::class,
                        'cetak',
                    ])->name('absensi.cetak');
                    Route::get('/absensi/export', [
                        LaporanAbsensiController::class,
                        'export',
                    ])->name('absensi.export');

                    Route::get('/keterlambatan', [
                        LaporanKeterlambatanController::class,
                        'index',
                    ])->name('keterlambatan.index');
                    Route::get('/keterlambatan/cetak', [
                        LaporanKeterlambatanController::class,
                        'cetak',
                    ])->name('keterlambatan.cetak');
                    Route::get('/keterlambatan/export', [
                        LaporanKeterlambatanController::class,
                        'export',
                    ])->name('keterlambatan.export');

                    Route::get('/jadwal-guru', [
                        LaporanJadwalGuruController::class,
                        'index',
                    ])->name('jadwal-guru.index');
                    Route::get('/jadwal-guru/cetak', [
                        LaporanJadwalGuruController::class,
                        'cetak',
                    ])->name('jadwal-guru.cetak');
                    Route::get('/jadwal-guru/export', [
                        LaporanJadwalGuruController::class,
                        'export',
                    ])->name('jadwal-guru.export');
                });
        });

    Route::put('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');
});
