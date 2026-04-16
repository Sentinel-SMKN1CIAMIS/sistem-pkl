<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kode', 'nama'])]
class ProgramKeahlian extends Model
{
    public function konsentrasiKeahlian()
    {
        return $this->hasMany(KonsentrasiKeahlian::class);
    }
}
