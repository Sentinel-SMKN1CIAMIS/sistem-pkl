<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPkl extends Model
{
    protected $fillable = [
        'siswa_id', 'judul', 'deskripsi', 
        'link_media_sosial', 'status', 'submitted_at'
    ];

    protected $casts = [
        'link_media_sosial' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
