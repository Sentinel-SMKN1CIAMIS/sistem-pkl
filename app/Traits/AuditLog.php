<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait AuditLog
{
    public static function bootAuditLog()
    {
        static::created(function ($model) {
            try {
                $model->recordActivity('CREATED', "Data {$model->getTable()} dengan ID #{$model->id} berhasil dibuat.");
            } catch (\Exception $e) {
                // Silently fail to prevent disrupting the main operation
                // IP geolocation is non-critical for the application flow
            }
        });

        static::updated(function ($model) {
            try {
                $model->recordActivity('UPDATED', "Data {$model->getTable()} dengan ID #{$model->id} telah diperbarui.");
            } catch (\Exception $e) {
                // Silently fail to prevent disrupting the main operation
            }
        });

        static::deleted(function ($model) {
            try {
                $model->recordActivity('DELETED', "Data {$model->getTable()} dengan ID #{$model->id} telah dihapus.");
            } catch (\Exception $e) {
                // Silently fail to prevent disrupting the main operation
            }
        });
    }

    public function recordActivity($action, $description)
    {
        $ip = Request::ip();
        $location = ActivityLog::getLocationFromIp($ip);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip,
            'location' => $location,
        ]);
    }
}
