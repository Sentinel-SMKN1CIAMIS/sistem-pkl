<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['konsentrasi_keahlian_id', 'nama', 'kategori', 'cp', 'tp', 'deskripsi'])]
class Kompetensi extends Model
{
    use AuditLog;

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class);
    }
}
