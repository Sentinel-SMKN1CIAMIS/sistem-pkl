<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['konsentrasi_keahlian_id', 'nama', 'alamat', 'latitude', 'longitude', 'kota', 'no_telepon', 'email', 'nama_pimpinan', 'kontak', 'jabatan', 'bidang_usaha', 'jenis_industri', 'zona_id', 'is_active'])]
class Dudi extends Model
{
    protected $table = 'dudis';

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function konsentrasiKeahlians()
    {
        return $this->belongsToMany(KonsentrasiKeahlian::class, 'dudi_konsentrasi_keahlian', 'dudi_id', 'konsentrasi_keahlian_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function pembimbingDudi()
    {
        return $this->hasMany(PembimbingDudi::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
