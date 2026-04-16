<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'konsentrasi_keahlian_id', 'dudi_id', 'pembimbing_sekolah_id', 'pembimbing_dudi_id', 'nis', 'nama_lengkap', 'kelas', 'jenis_kelamin', 'no_hp', 'alamat', 'tahun_ajaran', 'status_pkl'])]
class Siswa extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function pembimbingSekolah()
    {
        return $this->belongsTo(PembimbingSekolah::class);
    }

    public function pembimbingDudi()
    {
        return $this->belongsTo(PembimbingDudi::class);
    }

    public function jurnal()
    {
        return $this->hasMany(Jurnal::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
