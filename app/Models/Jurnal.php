<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $siswa_id
 * @property int $kompetensi_id
 * @property int|null $cp_id
 * @property string|null $cp
 * @property string $tanggal
 * @property string $deskripsi_pekerjaan
 * @property string|null $catatan
 * @property string|null $foto_path
 * @property string $status
 * @property string|null $catatan_pembimbing
 * @property string|null $catatan_guru
 * @property string|null $approval_status
 * @property string|null $approval_notes
 * @property int|null $approved_by
 * @property string|null $approved_at
 */
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
