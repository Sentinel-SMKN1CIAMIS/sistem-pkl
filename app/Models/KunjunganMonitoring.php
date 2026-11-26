<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Model;

class KunjunganMonitoring extends Model
{
    use AuditLog;

    protected $fillable = [
        'pembimbing_sekolah_id',
        'dudi_id',
        'tanggal_kunjungan',
        'catatan_monitoring',
        'kendala',
        'foto_path',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function pembimbingSekolah()
    {
        return $this->belongsTo(PembimbingSekolah::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }
}
