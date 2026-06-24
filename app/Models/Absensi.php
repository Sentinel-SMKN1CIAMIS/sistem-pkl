<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'siswa_id', 'tanggal', 'status', 'approval_status', 'approved_by', 'approval_note',
        'keterangan', 'alasan', 'waktu_datang', 'waktu_pulang', 'ttd_siswa_path', 
        'ttd_pembimbing_dudi_path', 'latitude', 'longitude',
        'early_leave_request_status', 'early_leave_reason', 'early_leave_requested_at',
        'early_leave_approved_by', 'early_leave_approved_at', 'early_leave_approval_note'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function earlyLeaveApprovedBy()
    {
        return $this->belongsTo(User::class, 'early_leave_approved_by');
    }
}
