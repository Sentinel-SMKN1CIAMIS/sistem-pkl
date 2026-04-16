<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuPanduan extends Model
{
    protected $fillable = [
        'judul', 'tipe', 'konsentrasi_keahlian_id', 
        'deskripsi', 'file_path'
    ];

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }
}
