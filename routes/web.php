<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BahanAjarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiKinerjaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalMengajarController;
use App\Http\Controllers\JurnalMengajarController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PengajuanIzinCutiController;
use App\Http\Controllers\PresensiGuruController;
use App\Http\Controllers\RekapBebanMengajarController;
use App\Http\Controllers\RppModulAjarController;
use App\Http\Controllers\SkTugasController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Data Guru — pengurus yayasan, kepala sekolah, admin sekolah
    Route::resource('guru', GuruController::class)
        ->middleware('role:pengurus_yayasan,kepala_sekolah,admin_sekolah');

    // Jadwal Mengajar
    Route::resource('jadwal-mengajar', JadwalMengajarController::class)
        ->middleware('role:pengurus_yayasan,kepala_sekolah,admin_sekolah,guru');

    // Presensi Guru
    Route::resource('presensi', PresensiGuruController::class)->except(['edit', 'update', 'show'])
        ->middleware('role:pengurus_yayasan,kepala_sekolah,admin_sekolah,guru');

    // Jurnal Mengajar
    Route::resource('jurnal', JurnalMengajarController::class)->except(['edit', 'update'])
        ->middleware('role:guru,kepala_sekolah,admin_sekolah');

    // RPP & Modul Ajar
    Route::resource('rpp', RppModulAjarController::class)->except(['edit', 'update'])
        ->middleware('role:guru,kepala_sekolah,admin_sekolah');
    Route::post('rpp/{rppModulAjar}/validasi', [RppModulAjarController::class, 'validasi'])
        ->name('rpp.validasi')
        ->middleware('role:kepala_sekolah');

    // Bahan Ajar
    Route::resource('bahan-ajar', BahanAjarController::class)->except(['edit', 'update'])
        ->middleware('role:guru,kepala_sekolah,admin_sekolah');

    // Penilaian
    Route::resource('penilaian', PenilaianController::class)->except(['edit', 'update', 'show'])
        ->middleware('role:guru,kepala_sekolah,admin_sekolah');

    // Pengajuan Izin / Cuti
    Route::resource('pengajuan-izin', PengajuanIzinCutiController::class)->except(['edit', 'update'])
        ->middleware('role:guru,kepala_sekolah,admin_sekolah');
    Route::post('pengajuan-izin/{pengajuanIzinCuti}/approve', [PengajuanIzinCutiController::class, 'approve'])
        ->name('pengajuan-izin.approve')
        ->middleware('role:kepala_sekolah,admin_sekolah');

    // Rekap Beban Mengajar
    Route::get('rekap-beban', [RekapBebanMengajarController::class, 'index'])->name('rekap-beban.index')
        ->middleware('role:pengurus_yayasan,kepala_sekolah,admin_sekolah,guru');
    Route::post('rekap-beban/generate', [RekapBebanMengajarController::class, 'generate'])
        ->name('rekap-beban.generate')
        ->middleware('role:kepala_sekolah,admin_sekolah');

    // SK Tugas
    Route::resource('sk-tugas', SkTugasController::class)->except(['edit', 'update'])
        ->middleware('role:pengurus_yayasan,kepala_sekolah,admin_sekolah,guru');

    // Evaluasi Kinerja
    Route::resource('evaluasi-kinerja', EvaluasiKinerjaController::class)->except(['edit', 'update'])
        ->middleware('role:pengurus_yayasan,kepala_sekolah,guru');
});
