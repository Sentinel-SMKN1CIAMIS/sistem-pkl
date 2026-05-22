<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesans';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'isi',
        'is_broadcast',
        'dibaca_at',
    ];

    protected $casts = [
        'dibaca_at' => 'datetime',
        'is_broadcast' => 'boolean',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function isRead(): bool
    {
        return $this->dibaca_at !== null;
    }
}
