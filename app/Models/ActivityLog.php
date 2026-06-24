<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class ActivityLog extends Model
{
    use Prunable;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'location'
    ];

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(90));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getLocationFromIp($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }

        return cache()->remember("ip_loc_" . md5($ip), now()->addDays(7), function () use ($ip) {
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
        });
    }
}
