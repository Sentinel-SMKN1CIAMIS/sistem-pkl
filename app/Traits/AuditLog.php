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
            $model->recordActivity('CREATED', "Data {$model->getTable()} dengan ID #{$model->id} berhasil dibuat.");
        });

        static::updated(function ($model) {
            $model->recordActivity('UPDATED', "Data {$model->getTable()} dengan ID #{$model->id} telah diperbarui.");
        });

        static::deleted(function ($model) {
            $model->recordActivity('DELETED', "Data {$model->getTable()} dengan ID #{$model->id} telah dihapus.");
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
