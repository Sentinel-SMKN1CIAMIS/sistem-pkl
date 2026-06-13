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

// Force Change Password Routes - Available during auth but guest on this specific route
Route::get('/auth/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'show'])
    ->middleware('auth')
    ->name('auth.change-password.show');
Route::patch('/auth/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])
    ->middleware('auth')
    ->name('auth.change-password.update');

Route::middleware('auth', 'force.password.change')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Redirects to correct dashboard based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Siswa Routes
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::resource('jurnal', \App\Http\Controllers\Siswa\JurnalController::class);
        Route::get('absensi', [\App\Http\Controllers\Siswa\AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('absensi/clock-in', [\App\Http\Controllers\Siswa\AbsensiController::class, 'clockIn'])->name('absensi.clock-in');
        Route::post('absensi/clock-out', [\App\Http\Controllers\Siswa\AbsensiController::class, 'clockOut'])->name('absensi.clock-out');
        Route::post('absensi/submit-absence-request', [\App\Http\Controllers\Siswa\AbsensiController::class, 'submitAbsenceRequest'])->name('absensi.submit-absence-request');
        Route::get('laporan', [\App\Http\Controllers\Siswa\LaporanController::class, 'index'])->name('laporan.index');
        Route::post('laporan', [\App\Http\Controllers\Siswa\LaporanController::class, 'store'])->name('laporan.store');
        Route::get('panduan', [\App\Http\Controllers\Siswa\PanduanController::class, 'index'])->name('panduan.index');
        Route::get('jurnal/export', [\App\Http\Controllers\Siswa\JurnalExportController::class, 'export'])->name('jurnal.export');
        Route::get('jurnal/portofolio', [\App\Http\Controllers\Siswa\JurnalExportController::class, 'portofolio'])->name('jurnal.portofolio');
        Route::get('jurnal/sertifikat', [\App\Http\Controllers\Siswa\JurnalExportController::class, 'sertifikat'])->name('jurnal.sertifikat');
        Route::get('profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'index'])->name('profile.index');
        Route::patch('profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-lokasi-dudi', [\App\Http\Controllers\Siswa\ProfileController::class, 'updateLokasiDudi'])->name('profile.update-lokasi-dudi');
        Route::get('bantuan', [\App\Http\Controllers\Siswa\BantuanController::class, 'index'])->name('bantuan.index');

        // Pengajuan PKL
        Route::get('pengajuan-pkl', [\App\Http\Controllers\Siswa\PengajuanPklController::class, 'create'])->name('pengajuan_pkl.create');
        Route::post('pengajuan-pkl', [\App\Http\Controllers\Siswa\PengajuanPklController::class, 'store'])->name('pengajuan_pkl.store');
        Route::get('pengajuan-pkl/status', [\App\Http\Controllers\Siswa\PengajuanPklController::class, 'status'])->name('pengajuan_pkl.status');
        Route::get('pengajuan-pkl/pembimbing', [\App\Http\Controllers\Siswa\PengajuanPklController::class, 'getPembimbing'])->name('pengajuan_pkl.pembimbing');
    });

    // Verification Routes (Review by Mentors)
    Route::middleware('role:pembimbing_sekolah')->prefix('pembimbing_sekolah')->name('pembimbing_sekolah.')->group(function () {
        Route::post('bulk-acc', [\App\Http\Controllers\DashboardController::class, 'bulkAcc'])->name('bulk_acc');
        Route::get('siswa', [\App\Http\Controllers\PembimbingSekolah\SiswaController::class, 'index'])->name('siswa.index');
        Route::get('jurnal', [\App\Http\Controllers\PembimbingSekolah\JurnalController::class, 'index'])->name('jurnal.index');
        Route::patch('jurnal/{jurnal}', [\App\Http\Controllers\PembimbingSekolah\JurnalController::class, 'update'])->name('jurnal.update');
        Route::post('jurnal/{jurnal}/approve', [\App\Http\Controllers\PembimbingSekolah\JurnalController::class, 'approve'])->name('jurnal.approve');
        Route::post('jurnal/{jurnal}/reject', [\App\Http\Controllers\PembimbingSekolah\JurnalController::class, 'reject'])->name('jurnal.reject');
        Route::get('absensi', [\App\Http\Controllers\PembimbingSekolah\AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/export', [\App\Http\Controllers\PembimbingSekolah\AbsensiController::class, 'export'])->name('absensi.export');
        Route::get('absensi/approval', [\App\Http\Controllers\PembimbingSekolah\AbsensiApprovalController::class, 'index'])->name('absensi.approval.index');
        Route::patch('absensi/{absensi}/approve', [\App\Http\Controllers\PembimbingSekolah\AbsensiApprovalController::class, 'approve'])->name('absensi.approve');
        Route::patch('absensi/{absensi}/reject', [\App\Http\Controllers\PembimbingSekolah\AbsensiApprovalController::class, 'reject'])->name('absensi.reject');
        Route::get('laporan', [\App\Http\Controllers\PembimbingSekolah\LaporanController::class, 'index'])->name('laporan.index');
        Route::patch('laporan/{laporan}', [\App\Http\Controllers\PembimbingSekolah\LaporanController::class, 'update'])->name('laporan.update');
    });

    Route::middleware('role:pembimbing_dudi')->prefix('pembimbing_dudi')->name('pembimbing_dudi.')->group(function () {
        Route::get('siswa', [\App\Http\Controllers\PembimbingDudi\SiswaController::class, 'index'])->name('siswa.index');
        Route::get('jurnal', [\App\Http\Controllers\PembimbingDudi\JurnalController::class, 'index'])->name('jurnal.index');
        Route::patch('jurnal/{jurnal}', [\App\Http\Controllers\PembimbingDudi\JurnalController::class, 'update'])->name('jurnal.update');
        Route::get('absensi', [\App\Http\Controllers\PembimbingDudi\AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('feedback', [\App\Http\Controllers\PembimbingDudi\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('feedback/create', [\App\Http\Controllers\PembimbingDudi\FeedbackController::class, 'create'])->name('feedback.create');
        Route::post('feedback', [\App\Http\Controllers\PembimbingDudi\FeedbackController::class, 'store'])->name('feedback.store');
    });
    
    // Kaprog Routes
    Route::middleware('role:kaprog')->prefix('kaprog')->name('kaprog.')->group(function () {
        Route::get('laporan', [\App\Http\Controllers\KaprogController::class, 'index'])->name('laporan.index');
        
        // Pengajuan PKL
        Route::get('pengajuan-pkl', [\App\Http\Controllers\Kaprog\PengajuanPklController::class, 'index'])->name('pengajuan_pkl.index');
        Route::patch('pengajuan-pkl/{pengajuanPkl}', [\App\Http\Controllers\Kaprog\PengajuanPklController::class, 'update'])->name('pengajuan_pkl.update');
    });

    // Shared Map Routes - Accessible by Pokja, Kaprog, Pembimbing Sekolah
    Route::middleware('role:pokja,kaprog,pembimbing_sekolah,super_admin')->group(function () {
        Route::get('peta-dudi', [\App\Http\Controllers\Pokja\PemetaanController::class, 'maps'])->name('shared.pemetaan.maps');
        Route::get('peta-dudi/data', [\App\Http\Controllers\Pokja\PemetaanController::class, 'mapsData'])->name('shared.pemetaan.maps.data');
    });

    // General Auth Routes
    Route::get('notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifications.index');
    Route::patch('notifikasi/{notifikasi}/read', [\App\Http\Controllers\NotifikasiController::class, 'markAsRead'])->name('notifications.read');
    Route::get('panduan-interaktif', [\App\Http\Controllers\PanduanInteraktifController::class, 'index'])->name('panduan.interaktif');

    // Pesan (Chat) Routes
    Route::get('pesan', [\App\Http\Controllers\PesanController::class, 'index'])->name('pesan.index');
    Route::get('pesan/broadcast', [\App\Http\Controllers\PesanController::class, 'index'])->name('pesan.broadcast.form');
    Route::post('pesan/broadcast', [\App\Http\Controllers\PesanController::class, 'broadcast'])->name('pesan.broadcast');
    Route::get('pesan/{user}', [\App\Http\Controllers\PesanController::class, 'show'])->name('pesan.show');
    Route::post('pesan/{user}', [\App\Http\Controllers\PesanController::class, 'store'])->name('pesan.store');
    Route::get('pesan/{user}/poll', [\App\Http\Controllers\PesanController::class, 'poll'])->name('pesan.poll');

    // Shared Admin Routes (Super Admin & Pokja)
    Route::middleware('role:super_admin,pokja')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('program_keahlian', \App\Http\Controllers\ProgramKeahlianController::class);
        Route::resource('konsentrasi_keahlian', \App\Http\Controllers\KonsentrasiKeahlianController::class);
    });

    // Super Admin Routes
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('kompetensi', \App\Http\Controllers\KompetensiController::class);
        Route::resource('panduan', \App\Http\Controllers\Admin\BukuPanduanController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('pokja-groups', \App\Http\Controllers\Admin\PokjaGroupController::class);
        Route::post('pokja-groups/{pokjaGroup}/add-member', [\App\Http\Controllers\Admin\PokjaGroupController::class, 'addMember'])->name('pokja-groups.add-member');
        Route::post('pokja-groups/{pokjaGroup}/remove-member', [\App\Http\Controllers\Admin\PokjaGroupController::class, 'removeMember'])->name('pokja-groups.remove-member');
        Route::get('logs', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('logs.index');
        Route::get('config', [\App\Http\Controllers\Admin\ConfigController::class, 'index'])->name('config.index');
        Route::post('config', [\App\Http\Controllers\Admin\ConfigController::class, 'update'])->name('config.update');
    });

    // Pokja Routes - with group membership check
    Route::middleware('role:pokja,super_admin', 'pokja-group')->prefix('pokja')->name('pokja.')->group(function () {
        Route::resource('kompetensi', \App\Http\Controllers\Pokja\KompetensiController::class);
        Route::resource('siswa', \App\Http\Controllers\SiswaController::class);
        Route::resource('dudi', \App\Http\Controllers\DudiController::class);
        Route::resource('pembimbing_sekolah', \App\Http\Controllers\PembimbingSekolahController::class);
        Route::resource('pembimbing_dudi', \App\Http\Controllers\PembimbingDudiController::class);
        Route::resource('kaprog', \App\Http\Controllers\Pokja\KaprogController::class);
        Route::get('pemetaan', [\App\Http\Controllers\Pokja\PemetaanController::class, 'index'])->name('pemetaan.index');
        Route::get('pemetaan/maps', [\App\Http\Controllers\Pokja\PemetaanController::class, 'maps'])->name('pemetaan.maps');
        Route::get('pemetaan/maps/data', [\App\Http\Controllers\Pokja\PemetaanController::class, 'mapsData'])->name('pemetaan.maps.data');

        // Zona CRUD
        Route::get('zona', [\App\Http\Controllers\Pokja\ZonaController::class, 'index'])->name('zona.index');
        Route::post('zona', [\App\Http\Controllers\Pokja\ZonaController::class, 'store'])->name('zona.store');
        Route::put('zona/{zona}', [\App\Http\Controllers\Pokja\ZonaController::class, 'update'])->name('zona.update');
        Route::delete('zona/{zona}', [\App\Http\Controllers\Pokja\ZonaController::class, 'destroy'])->name('zona.destroy');
        Route::get('zona/geojson', [\App\Http\Controllers\Pokja\ZonaController::class, 'geojson'])->name('zona.geojson');

        Route::get('monitoring', [\App\Http\Controllers\Pokja\MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('evaluasi', [\App\Http\Controllers\Pokja\EvaluasiController::class, 'index'])->name('evaluasi.index');
        Route::get('feedback', [\App\Http\Controllers\Pokja\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('feedback/{feedback}/print', [\App\Http\Controllers\Pokja\FeedbackController::class, 'print'])->name('feedback.print');

        // Pengaturan Pokja
        Route::get('pengaturan/sertifikat', [\App\Http\Controllers\Pokja\PengaturanController::class, 'sertifikat'])->name('pengaturan.sertifikat');
        Route::post('pengaturan/sertifikat', [\App\Http\Controllers\Pokja\PengaturanController::class, 'updateSertifikat'])->name('pengaturan.sertifikat.update');

        // Import & Template Routes
        Route::get('import/panduan', [\App\Http\Controllers\Pokja\ImportController::class, 'showPanduan'])->name('import.panduan');
        Route::get('import/template/{type}', [\App\Http\Controllers\Pokja\ImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('siswa/import', [\App\Http\Controllers\Pokja\ImportController::class, 'importSiswa'])->name('siswa.import');
        Route::post('dudi/import', [\App\Http\Controllers\Pokja\ImportController::class, 'importDudi'])->name('dudi.import');
        Route::post('pembimbing-sekolah/import', [\App\Http\Controllers\Pokja\ImportController::class, 'importPembimbingSekolah'])->name('pembimbing_sekolah.import');
        Route::post('pembimbing-dudi/import', [\App\Http\Controllers\Pokja\ImportController::class, 'importPembimbingDudi'])->name('pembimbing_dudi.import');
        Route::post('kaprog/import', [\App\Http\Controllers\Pokja\ImportController::class, 'importKaprog'])->name('kaprog.import');
    });
});

