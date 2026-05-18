<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'dudi_id', 'nama_lengkap', 'jabatan', 'no_hp'])]
class PembimbingDudi extends Model
{
    protected $table = 'pembimbing_dudis';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'pembimbing_dudi_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'pembimbing_dudi_id');
    }
}
