<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'siswa_id', 'tanggal', 'status', 'approval_status', 'approved_by', 'approval_note',
        'keterangan', 'alasan', 'waktu_datang', 'waktu_pulang', 'ttd_siswa_path', 
        'ttd_pembimbing_dudi_path', 'latitude', 'longitude'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
