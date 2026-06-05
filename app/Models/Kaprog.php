<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'konsentrasi_keahlian_id'])]
class Kaprog extends Model
{
    protected $table = 'kaprog';

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
        return $this->konsentrasiKeahlian->siswa();
    }
}
