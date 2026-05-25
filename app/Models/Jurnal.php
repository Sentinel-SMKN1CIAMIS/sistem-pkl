<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use AuditLog;
    protected $fillable = [
        'siswa_id', 'kompetensi_id', 'cp', 'tanggal', 
        'deskripsi_pekerjaan', 'catatan', 'foto_path', 
        'status', 'catatan_pembimbing', 'catatan_guru'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kompetensi()
    {
        return $this->belongsTo(Kompetensi::class);
    }
}
