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

        // Load dynamic configuration from database
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('konfigurasi_sistems')) {
                $configs = \App\Models\KonfigurasiSistem::all();
                
                $appName = $configs->where('key', 'app_name')->first()?->value;
                if ($appName) {
                    config(['app.name' => $appName]);
                }
                
                $appLogoUrl = $configs->where('key', 'app_logo_url')->first()?->value;
                if ($appLogoUrl) {
                    config(['app.logo_url' => $appLogoUrl]);
                }

                $tahunAjaran = $configs->where('key', 'tahun_ajaran')->first()?->value;
                if ($tahunAjaran) {
                    config(['app.tahun_ajaran' => $tahunAjaran]);
                }

                $kontakAdmin = $configs->where('key', 'kontak_admin')->first()?->value;
                if ($kontakAdmin) {
                    config(['app.kontak_admin' => $kontakAdmin]);
                }

                // Bagikan variabel ke semua view
                view()->share('appName', $appName ?: 'MAS-PKL');
                view()->share('tahunAjaranActive', $tahunAjaran ?: '-');
                view()->share('appLogoActive', $appLogoUrl ?: null);
                view()->share('kontakAdminActive', $kontakAdmin ?: '-');
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum dimigrasikan
        }
    }
}
