<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogAuthenticationActions
{
    public function handleLogin(Login $event)
    {
        $ip = Request::ip();
        ActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'LOGIN',
            'description' => "User {$event->user->username} berhasil login ke sistem.",
            'ip_address' => $ip,
            'location' => ActivityLog::getLocationFromIp($ip),
        ]);
    }

    public function handleLogout(Logout $event)
    {
        if ($event->user) {
            $ip = Request::ip();
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'LOGOUT',
                'description' => "User {$event->user->username} telah logout dari sistem.",
                'ip_address' => $ip,
                'location' => ActivityLog::getLocationFromIp($ip),
            ]);
        }
    }
}
