<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPkl extends Model
{
    protected $fillable = [
        'siswa_id', 'judul', 'deskripsi', 
        'file_path', 'status', 'submitted_at'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
