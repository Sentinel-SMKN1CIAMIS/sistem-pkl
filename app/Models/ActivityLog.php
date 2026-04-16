<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getLocationFromIp($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}?fields=city,regionName,country");
            
            if ($response->successful()) {
                $data = $response->json();
                return ($data['city'] ?? '') . ', ' . ($data['regionName'] ?? '') . ' (' . ($data['country'] ?? '') . ')';
            }
        } catch (\Exception $e) {
            // Silence is golden
        }

        return 'Unknown';
    }
}
