<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringPembimbing extends Model
{
    protected $fillable = [
        'pembimbing_sekolah_id', 'pokja_user_id', 
        'tanggal', 'catatan', 'status'
    ];

    public function pembimbingSekolah()
    {
        return $this->belongsTo(PembimbingSekolah::class);
    }

    public function pokjaUser()
    {
        return $this->belongsTo(User::class, 'pokja_user_id');
    }
}
