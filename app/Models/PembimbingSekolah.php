<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'konsentrasi_keahlian_id', 'nip', 'nama_lengkap', 'tipe', 'no_hp', 'kapasitas'])]
class PembimbingSekolah extends Model
{
    protected $table = 'pembimbing_sekolahs';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'pembimbing_sekolah_id');
    }

    public function kelasDiajar()
    {
        return $this->hasMany(KelasPembimbing::class, 'pembimbing_sekolah_id');
    }
}
