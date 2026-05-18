<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'pembimbing_dudi_id',
        'periode',
        'isi_feedback',
        'saran',
    ];

    public function pembimbingDudi()
    {
        return $this->belongsTo(PembimbingDudi::class);
    }
}
