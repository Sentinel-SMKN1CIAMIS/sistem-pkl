<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $siswa = User::where('role', 'siswa')->first();

        $logs = [
            [
                'user_id' => $admin->id,
                'action' => 'LOGIN',
                'description' => "User {$admin->username} berhasil login ke sistem.",
                'created_at' => now()->subHours(5),
            ],
            [
                'user_id' => $admin->id,
                'action' => 'CREATED',
                'description' => "Data users dengan ID #3 berhasil dibuat.",
                'created_at' => now()->subHours(4),
            ],
            [
                'user_id' => $siswa->id,
                'action' => 'LOGIN',
                'description' => "User {$siswa->username} berhasil login ke sistem.",
                'created_at' => now()->subHours(3),
            ],
            [
                'user_id' => $siswa->id,
                'action' => 'CREATED',
                'description' => "Data jurnals dengan ID #1 berhasil dibuat.",
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $admin->id,
                'action' => 'LOGOUT',
                'description' => "User {$admin->username} telah logout dari sistem.",
                'created_at' => now()->subHour(),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create(array_merge($log, ['ip_address' => '127.0.0.1']));
        }
    }
}
