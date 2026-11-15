<?php

namespace App\Services;

use App\Models\KonfigurasiSistem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MaintenanceService
{
    public const CACHE_KEY = 'app_maintenance_state';

    public const ALL_ROLES = [
        'super_admin' => [
            'label' => 'Super Administrator',
            'desc' => 'Admin pengelola sistem. Memiliki akses penuh dan selalu aktif.',
            'locked' => true,
        ],
        'pokja' => [
            'label' => 'Pokja PKL',
            'desc' => 'Panitia kelompok kerja PKL (distribusi, bimbingan, pemetaan).',
            'locked' => false,
        ],
        'kepala_sekolah' => [
            'label' => 'Kepala Sekolah',
            'desc' => 'Akses pemantauan eksekutif dan laporan.',
            'locked' => false,
        ],
        'kaprog' => [
            'label' => 'Kepala Program (Kaprog)',
            'desc' => 'Ketua jurusan dan verifikasi tempat PKL.',
            'locked' => false,
        ],
        'pembimbing_sekolah' => [
            'label' => 'Guru Pembimbing Sekolah',
            'desc' => 'Bimbingan, monitoring, dan validasi absensi/jurnal.',
            'locked' => false,
        ],
        'pembimbing_dudi' => [
            'label' => 'Pembimbing DUDI / Perusahaan',
            'desc' => 'Instruktur/mentor di tempat industri.',
            'locked' => false,
        ],
        'siswa' => [
            'label' => 'Siswa PKL',
            'desc' => 'Pengisian absensi harian dan log kegiatan PKL.',
            'locked' => false,
        ],
    ];

    /**
     * Cek apakah sistem sedang dalam mode maintenance.
     */
    public static function isMaintenanceActive(): bool
    {
        return (string) self::getSetting('maintenance_mode', '0') === '1';
    }

    /**
     * Cek apakah suatu peran (role) diizinkan mengakses saat maintenance aktif.
     */
    public static function isRoleAllowed(?string $role): bool
    {
        if (empty($role)) {
            return false;
        }

        // Super Admin selalu memiliki akses penuh tanpa kecuali
        if ($role === 'super_admin') {
            return true;
        }

        $allowedRoles = self::getAllowedRoles();
        return in_array($role, $allowedRoles, true);
    }

    /**
     * Dapatkan daftar peran yang diizinkan saat maintenance aktif.
     */
    public static function getAllowedRoles(): array
    {
        $raw = self::getSetting('maintenance_allowed_roles', '["super_admin"]');
        if (is_array($raw)) {
            $roles = $raw;
        } else {
            $roles = json_decode((string) $raw, true) ?: [];
        }

        if (!in_array('super_admin', $roles, true)) {
            $roles[] = 'super_admin';
        }

        return array_values(array_unique($roles));
    }

    /**
     * Dapatkan pesan maintenance kustom.
     */
    public static function getMessage(): string
    {
        return (string) self::getSetting(
            'maintenance_message',
            'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa dan stabilitas data. Akses untuk beberapa peran dibatasi sementara.'
        );
    }

    /**
     * Simpan status dan pengaturan mode maintenance.
     */
    public static function updateSettings(bool $active, array $allowedRoles = [], ?string $message = null, ?User $byUser = null): void
    {
        if (!in_array('super_admin', $allowedRoles, true)) {
            $allowedRoles[] = 'super_admin';
        }

        $cleanRoles = array_values(array_intersect($allowedRoles, array_keys(self::ALL_ROLES)));

        self::setSetting('maintenance_mode', $active ? '1' : '0');
        self::setSetting('maintenance_allowed_roles', json_encode($cleanRoles));

        if ($message !== null) {
            self::setSetting('maintenance_message', trim($message));
        }

        if ($active) {
            self::setSetting('maintenance_activated_at', now()->toDateTimeString());
            if ($byUser) {
                self::setSetting('maintenance_activated_by', $byUser->name ?? $byUser->username);
            }
        } else {
            self::setSetting('maintenance_deactivated_at', now()->toDateTimeString());
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Aktifkan mode maintenance.
     */
    public static function enable(array $allowedRoles = [], ?string $message = null, ?User $byUser = null): void
    {
        self::updateSettings(true, $allowedRoles, $message, $byUser);
    }

    /**
     * Matikan mode maintenance.
     */
    public static function disable(): void
    {
        self::setSetting('maintenance_mode', '0');
        self::setSetting('maintenance_deactivated_at', now()->toDateTimeString());
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Dapatkan seluruh ringkasan status maintenance.
     */
    public static function getSummary(): array
    {
        return [
            'is_active' => self::isMaintenanceActive(),
            'message' => self::getMessage(),
            'allowed_roles' => self::getAllowedRoles(),
            'activated_at' => self::getSetting('maintenance_activated_at'),
            'activated_by' => self::getSetting('maintenance_activated_by'),
            'deactivated_at' => self::getSetting('maintenance_deactivated_at'),
        ];
    }

    /**
     * Ambil setting dari cache atau database.
     */
    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $all = Cache::remember(self::CACHE_KEY, 3600, function () {
            try {
                return KonfigurasiSistem::where('key', 'like', 'maintenance_%')
                    ->pluck('value', 'key')
                    ->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });

        return $all[$key] ?? $default;
    }

    /**
     * Simpan setting ke database dan hapus cache.
     */
    public static function setSetting(string $key, mixed $value): void
    {
        try {
            KonfigurasiSistem::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        } catch (\Throwable $e) {
            // Silently fail if DB down
        }

        Cache::forget(self::CACHE_KEY);
    }
}

