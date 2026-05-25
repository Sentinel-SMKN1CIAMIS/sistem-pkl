<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['siswa_id', 'dudi_id', 'nama_perusahaan', 'pimpinan', 'alamat', 'kota', 'no_telp', 'status', 'catatan', 'acc_oleh'])]
class PengajuanPkl extends Model
{
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function accOleh()
    {
        return $this->belongsTo(User::class, 'acc_oleh');
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }
}
