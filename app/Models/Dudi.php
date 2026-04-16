<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['konsentrasi_keahlian_id', 'nama', 'alamat', 'kota', 'no_telepon', 'email', 'nama_pimpinan', 'bidang_usaha', 'is_active'])]
class Dudi extends Model
{
    protected $table = 'dudis';

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function pembimbingDudi()
    {
        return $this->hasMany(PembimbingDudi::class);
    }
}
