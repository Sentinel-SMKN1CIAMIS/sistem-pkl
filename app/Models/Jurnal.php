<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use AuditLog;
    protected $fillable = [
        'siswa_id', 'kompetensi_id', 'cp_id', 'cp', 'tanggal', 
        'deskripsi_pekerjaan', 'catatan', 'foto_path', 
        'status', 'catatan_pembimbing', 'catatan_guru',
        'approval_status', 'approval_notes', 'approved_by', 'approved_at'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kompetensi()
    {
        return $this->belongsTo(Kompetensi::class);
    }

    /**
     * Relationship to CP/TP master (Kompetensi used as TP master)
     */
    public function tujuanPembelajaran()
    {
        return $this->belongsTo(Kompetensi::class, 'cp_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the maximum backdate allowed (in days)
     */
    public static function getMaxBackdateDays()
    {
        return 7;
    }

    /**
     * Check if a date is within the allowed backdate window
     */
    public static function isDateAllowedForEntry($date)
    {
        $dateCarbon = \Carbon\Carbon::parse($date)->startOfDay();
        $today = \Carbon\Carbon::today();
        $maxPastDate = $today->copy()->subDays(static::getMaxBackdateDays());

        return $dateCarbon >= $maxPastDate && $dateCarbon <= $today;
    }
}
