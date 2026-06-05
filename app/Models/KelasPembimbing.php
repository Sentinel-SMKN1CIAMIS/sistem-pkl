<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['pembimbing_sekolah_id', 'kelas'])]
class KelasPembimbing extends Model
{
    protected $table = 'kelas_pembimbing';

    public function pembimbingSekolah()
    {
        return $this->belongsTo(PembimbingSekolah::class);
    }
}
