<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['program_keahlian_id', 'kode', 'nama', 'durasi_pkl_bulan', 'tanggal_mulai_pkl', 'tanggal_selesai_pkl'])]
class KonsentrasiKeahlian extends Model
{
    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function kompetensi()
    {
        return $this->hasMany(Kompetensi::class);
    }

    public function dudi()
    {
        return $this->hasMany(Dudi::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
