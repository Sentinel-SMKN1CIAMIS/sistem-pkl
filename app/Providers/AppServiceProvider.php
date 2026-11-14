<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->isSecure() || request()->header('x-forwarded-proto') === 'https' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::defaultView('partials.pagination');

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            [\App\Listeners\LogAuthenticationActions::class, 'handleLogin']
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            [\App\Listeners\LogAuthenticationActions::class, 'handleLogout']
        );

        // Register Global Navigation Items for Sidebar and Bottom Nav
        view()->composer(['layouts.app', 'layouts.partials.*'], function ($view) {
            $user = auth()->user();
            $role = $user?->role;
            $navItems = [];

            if ($role === 'siswa') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    ['name' => 'Jurnal Kegiatan', 'route' => 'siswa.jurnal.index', 'icon' => 'book-open'],
                    ['name' => 'Daftar Hadir', 'route' => 'siswa.absensi.index', 'icon' => 'calendar-check'],
                    ['name' => 'Laporan PKL', 'route' => 'siswa.laporan.index', 'icon' => 'file-text'],
                    ['name' => 'Buku Panduan', 'route' => 'siswa.panduan.index', 'icon' => 'library'],
                    ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
                    ['name' => 'Pusat Bantuan', 'route' => 'siswa.bantuan.index', 'icon' => 'help-circle'],
                ];
            } elseif ($role === 'pembimbing_sekolah') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    ['name' => 'Siswa Bimbingan', 'route' => 'pembimbing_sekolah.siswa.index', 'icon' => 'users'],
                    ['name' => 'Validasi Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
                    ['name' => 'Kehadiran Siswa', 'route' => 'pembimbing_sekolah.absensi.index', 'icon' => 'calendar'],
                    ['name' => 'Persetujuan Absensi', 'route' => 'pembimbing_sekolah.absensi.approval.index', 'icon' => 'check-circle'],
                    ['name' => 'Evaluasi Laporan', 'route' => 'pembimbing_sekolah.laporan.index', 'icon' => 'file-check'],
                    ['name' => 'Peta DUDI', 'route' => 'shared.pemetaan.maps', 'icon' => 'map'],
                    ['name' => 'Akun Mentor DUDI', 'route' => 'pembimbing_sekolah.dudi_mentor.index', 'icon' => 'building-2'],
                    ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
                ];
            } elseif ($role === 'pembimbing_dudi') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    ['name' => 'Siswa PKL', 'route' => 'pembimbing_dudi.siswa.index', 'icon' => 'users'],
                    ['name' => 'Jurnal Siswa', 'route' => 'pembimbing_dudi.jurnal.index', 'icon' => 'file-check-2'],
                    ['name' => 'Validasi Kehadiran', 'route' => 'pembimbing_dudi.absensi.index', 'icon' => 'clipboard-check'],
                    ['name' => 'Feedback Sekolah', 'route' => 'pembimbing_dudi.feedback.index', 'icon' => 'message-square-plus'],
                    ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
                ];
            } elseif ($role === 'pokja' || $role === 'kepala_sekolah') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    [
                        'name' => 'Kelola Akun',
                        'icon' => 'users',
                        'children' => [
                            ['name' => 'Data Siswa', 'route' => 'pokja.siswa.index', 'icon' => 'graduation-cap'],
                            ['name' => 'Pembimbing Sekolah', 'route' => 'pokja.pembimbing_sekolah.index', 'icon' => 'user-check'],
                            ['name' => 'Pembimbing DUDI', 'route' => 'pokja.pembimbing_dudi.index', 'icon' => 'user-cog'],
                            ['name' => 'Akun Kaprog', 'route' => 'pokja.kaprog.index', 'icon' => 'award'],
                            ['name' => 'Data DUDI', 'route' => 'pokja.dudi.index', 'icon' => 'building-2'],
                        ]
                    ],
                    [
                        'name' => 'Akademik',
                        'icon' => 'book',
                        'children' => [
                            ['name' => 'Program Keahlian', 'route' => 'admin.program_keahlian.index', 'icon' => 'book-open'],
                            ['name' => 'Konsentrasi Keahlian', 'route' => 'admin.konsentrasi_keahlian.index', 'icon' => 'layers'],
                            ['name' => 'Kelola TP/CP', 'route' => 'pokja.kompetensi.index', 'icon' => 'target'],
                        ]
                    ],
                    [
                        'name' => 'Pemetaan & Penempatan',
                        'icon' => 'network',
                        'children' => [
                            ['name' => 'Validasi Pengajuan', 'route' => 'pokja.pengajuan_pkl.index', 'icon' => 'file-check'],
                            ['name' => 'Peta DUDI', 'route' => 'shared.pemetaan.maps', 'icon' => 'map'],
                            ['name' => 'Pemetaan Siswa', 'route' => 'pokja.pemetaan.index', 'icon' => 'user-plus'],
                        ]
                    ],
                    [
                        'name' => 'Monitoring & Evaluasi',
                        'icon' => 'activity',
                        'children' => [
                            ['name' => 'Monitoring Pembimbing', 'route' => 'pokja.monitoring.index', 'icon' => 'eye'],
                            ['name' => 'Evaluasi PKL', 'route' => 'pokja.evaluasi.index', 'icon' => 'bar-chart-3'],
                            ['name' => 'Feedback DUDI', 'route' => 'pokja.feedback.index', 'icon' => 'message-square'],
                        ]
                    ],
                ];

                if ($role !== 'kepala_sekolah') {
                    $navItems[] = ['name' => 'Custom Report & Pivot', 'route' => 'pokja.custom_report.index', 'icon' => 'file-spreadsheet'];
                    $navItems[] = [
                        'name' => 'Pengaturan',
                        'icon' => 'settings',
                        'children' => [
                            ['name' => 'Custom Report & Ekspor', 'route' => 'pokja.custom_report.index', 'icon' => 'file-spreadsheet'],
                            ['name' => 'Template Sertifikat', 'route' => 'pokja.pengaturan.sertifikat', 'icon' => 'award'],
                            ['name' => 'Template Surat PKL', 'route' => 'pokja.pengaturan.surat_pengantar', 'icon' => 'file-text'],
                            ['name' => 'Kop Laporan PDF', 'route' => 'pokja.pengaturan.kop_laporan', 'icon' => 'building'],
                        ]
                    ];
                }

                $navItems[] = ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'];
            } elseif ($role === 'kaprog') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    ['name' => 'Pengajuan PKL', 'route' => 'kaprog.pengajuan_pkl.index', 'icon' => 'file-plus-2'],
                    ['name' => 'Data DUDI', 'route' => 'kaprog.dudi.index', 'icon' => 'building-2'],
                    ['name' => 'Peta DUDI', 'route' => 'shared.pemetaan.maps', 'icon' => 'map'],
                    ['name' => 'Monitoring Pembimbing', 'route' => 'kaprog.monitoring.index', 'icon' => 'eye'],
                    ['name' => 'Laporan Kaprog', 'route' => 'kaprog.laporan.index', 'icon' => 'file-bar-chart-2'],
                    ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
                ];
            } elseif ($role === 'super_admin') {
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
                    [
                        'name' => 'Kelola Akun',
                        'icon' => 'users',
                        'children' => [
                            ['name' => 'Data Siswa', 'route' => 'pokja.siswa.index', 'icon' => 'graduation-cap'],
                            ['name' => 'Pembimbing Sekolah', 'route' => 'pokja.pembimbing_sekolah.index', 'icon' => 'user-check'],
                            ['name' => 'Pembimbing DUDI', 'route' => 'pokja.pembimbing_dudi.index', 'icon' => 'user-cog'],
                            ['name' => 'Akun Kaprog', 'route' => 'pokja.kaprog.index', 'icon' => 'award'],
                            ['name' => 'Data DUDI', 'route' => 'pokja.dudi.index', 'icon' => 'building-2'],
                            ['name' => 'Akun Sistem', 'route' => 'admin.users.index', 'icon' => 'user'],
                        ]
                    ],
                    [
                        'name' => 'Akademik',
                        'icon' => 'book',
                        'children' => [
                            ['name' => 'Program Keahlian', 'route' => 'admin.program_keahlian.index', 'icon' => 'book-open'],
                            ['name' => 'Konsentrasi Keahlian', 'route' => 'admin.konsentrasi_keahlian.index', 'icon' => 'layers'],
                            ['name' => 'Kompetensi', 'route' => 'admin.kompetensi.index', 'icon' => 'target'],
                        ]
                    ],
                    ['name' => 'Custom Report & Pivot', 'route' => 'admin.custom_report.index', 'icon' => 'file-spreadsheet'],
                    [
                        'name' => 'Pengaturan',
                        'icon' => 'settings',
                        'children' => [
                            ['name' => 'Custom Report & Ekspor', 'route' => 'admin.custom_report.index', 'icon' => 'file-spreadsheet'],
                            ['name' => 'Konfigurasi Sistem', 'route' => 'admin.config.index', 'icon' => 'sliders'],
                        ]
                    ],
                    ['name' => 'Konfigurasi Sistem', 'route' => 'admin.config.index', 'icon' => 'sliders'],
                    ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
                    ['name' => 'Log Sistem', 'route' => 'admin.logs.index', 'icon' => 'scroll-text'],
                ];
            }

            if ($role !== 'pembimbing_sekolah' && $user?->pembimbingSekolah) {
                $navItems[] = [
                    'name' => 'Menu Pembimbing',
                    'icon' => 'user-check',
                    'children' => [
                        ['name' => 'Siswa Bimbingan', 'route' => 'pembimbing_sekolah.siswa.index', 'icon' => 'users'],
                        ['name' => 'Validasi Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
                        ['name' => 'Kehadiran Siswa', 'route' => 'pembimbing_sekolah.absensi.index', 'icon' => 'calendar'],
                        ['name' => 'Persetujuan Absensi', 'route' => 'pembimbing_sekolah.absensi.approval.index', 'icon' => 'check-circle'],
                        ['name' => 'Evaluasi Laporan', 'route' => 'pembimbing_sekolah.laporan.index', 'icon' => 'file-check'],
                    ]
                ];
            }

            $navItems[] = ['name' => 'Panduan Interaktif', 'route' => 'panduan.interaktif', 'icon' => 'sparkles'];

            $view->with('navItems', $navItems);
        });

        // Set default view variables first (especially for PHPUnit tests before migrations are run)
        view()->share('appName', 'MAS-PKL');
        view()->share('tahunAjaranActive', '-');
        view()->share('appLogoActive', null);
        view()->share('kontakAdminActive', '-');

        // Load dynamic configuration from database
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('konfigurasi_sistems')) {
                $configs = \App\Models\KonfigurasiSistem::all();
                
                $appName = $configs->where('key', 'app_name')->first()?->value;
                if ($appName) {
                    config(['app.name' => $appName]);
                    view()->share('appName', $appName);
                }
                
                $appLogoUrl = $configs->where('key', 'app_logo_url')->first()?->value;
                if ($appLogoUrl) {
                    config(['app.logo_url' => $appLogoUrl]);
                    view()->share('appLogoActive', $appLogoUrl);
                }

                $tahunAjaran = $configs->where('key', 'tahun_ajaran')->first()?->value;
                if ($tahunAjaran) {
                    config(['app.tahun_ajaran' => $tahunAjaran]);
                    view()->share('tahunAjaranActive', $tahunAjaran);
                }

                $kontakAdmin = $configs->where('key', 'kontak_admin')->first()?->value;
                if ($kontakAdmin) {
                    config(['app.kontak_admin' => $kontakAdmin]);
                    view()->share('kontakAdminActive', $kontakAdmin);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum dimigrasikan
        }

        // [SECURITY FIX LOW-03] Tambahkan HTTP Security Headers pada setiap response
        // Melindungi dari clickjacking, MIME sniffing, dan kebocoran Referer
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Foundation\Http\Events\RequestHandled::class,
            function ($event) {
                $response = $event->response;
                if (method_exists($response, 'headers')) {
                    // Cegah clickjacking: halaman tidak boleh di-embed di iframe dari domain lain
                    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                    // Cegah MIME sniffing: browser harus ikuti Content-Type yang dideklarasikan
                    $response->headers->set('X-Content-Type-Options', 'nosniff');
                    // Batasi informasi referer yang dikirim ke server lain
                    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                    // Batasi akses ke fitur browser yang sensitif (kamera, mikrofon, lokasi)
                    $response->headers->set('Permissions-Policy', 'geolocation=(self), camera=(), microphone=()');
                    // XSS protection untuk browser lama
                    $response->headers->set('X-XSS-Protection', '1; mode=block');
                }
            }
        );
    }
}
