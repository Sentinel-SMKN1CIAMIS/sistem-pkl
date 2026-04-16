<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Redirects to correct dashboard based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Siswa Routes
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::resource('jurnal', \App\Http\Controllers\Siswa\JurnalController::class);
        Route::get('absensi', [\App\Http\Controllers\Siswa\AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('absensi/clock-in', [\App\Http\Controllers\Siswa\AbsensiController::class, 'clockIn'])->name('absensi.clock-in');
        Route::post('absensi/clock-out', [\App\Http\Controllers\Siswa\AbsensiController::class, 'clockOut'])->name('absensi.clock-out');
        Route::get('laporan', [\App\Http\Controllers\Siswa\LaporanController::class, 'index'])->name('laporan.index');
        Route::post('laporan', [\App\Http\Controllers\Siswa\LaporanController::class, 'store'])->name('laporan.store');
        Route::get('panduan', [\App\Http\Controllers\Siswa\PanduanController::class, 'index'])->name('panduan.index');
        Route::get('jurnal/export', [\App\Http\Controllers\Siswa\JurnalExportController::class, 'export'])->name('jurnal.export');
    });

    // Verification Routes (Review by Mentors)
    Route::middleware('role:pembimbing_sekolah')->prefix('pembimbing_sekolah')->name('pembimbing_sekolah.')->group(function () {
        Route::get('siswa', function() { return 'Siswa Bimbingan'; })->name('siswa.index');
        Route::get('jurnal', [\App\Http\Controllers\PembimbingSekolah\JurnalController::class, 'index'])->name('jurnal.index');
        Route::get('absensi', [\App\Http\Controllers\PembimbingSekolah\AbsensiController::class, 'index'])->name('absensi.index');
    });

    Route::middleware('role:pembimbing_dudi')->prefix('pembimbing_dudi')->name('pembimbing_dudi.')->group(function () {
        Route::get('siswa', function() { return 'Siswa PKL'; })->name('siswa.index');
        Route::get('jurnal', [\App\Http\Controllers\PembimbingDudi\JurnalController::class, 'index'])->name('jurnal.index');
        Route::patch('jurnal/{jurnal}', [\App\Http\Controllers\PembimbingDudi\JurnalController::class, 'update'])->name('jurnal.update');
        Route::get('absensi', [\App\Http\Controllers\PembimbingDudi\AbsensiController::class, 'index'])->name('absensi.index');
    });
    
    // General Auth Routes
    Route::get('notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifications.index');
    Route::patch('notifikasi/{notifikasi}/read', [\App\Http\Controllers\NotifikasiController::class, 'markAsRead'])->name('notifications.read');

    // Super Admin Routes
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('program_keahlian', \App\Http\Controllers\ProgramKeahlianController::class);
        Route::resource('konsentrasi_keahlian', \App\Http\Controllers\KonsentrasiKeahlianController::class);
        Route::resource('kompetensi', \App\Http\Controllers\KompetensiController::class);
        Route::resource('panduan', \App\Http\Controllers\Admin\BukuPanduanController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::get('logs', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('logs.index');
        Route::get('config', [\App\Http\Controllers\Admin\ConfigController::class, 'index'])->name('config.index');
        Route::post('config', [\App\Http\Controllers\Admin\ConfigController::class, 'update'])->name('config.update');
    });

    // Pokja Routes
    Route::middleware('role:pokja,super_admin')->prefix('pokja')->name('pokja.')->group(function () {
        Route::resource('siswa', \App\Http\Controllers\SiswaController::class);
        Route::resource('dudi', \App\Http\Controllers\DudiController::class);
        Route::resource('pembimbing_sekolah', \App\Http\Controllers\PembimbingSekolahController::class);
        Route::resource('pembimbing_dudi', \App\Http\Controllers\PembimbingDudiController::class);
        Route::get('pemetaan', function() { return 'Pemetaan'; })->name('pemetaan.index');
        Route::get('monitoring', [\App\Http\Controllers\Pokja\MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('evaluasi', function() { return 'Evaluasi'; })->name('evaluasi.index');
    });
});
