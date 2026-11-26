<?php

namespace App\Models;

use App\Traits\AuditLog;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class HariLiburNasional extends Model
{
    use AuditLog;

    protected $fillable = [
        'tanggal',
        'nama',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Find national holiday record for a given date, if any.
     */
    public static function findByDate(CarbonInterface|string $date): ?self
    {
        $dateStr = $date instanceof CarbonInterface ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return static::whereDate('tanggal', $dateStr)->first();
    }
}
