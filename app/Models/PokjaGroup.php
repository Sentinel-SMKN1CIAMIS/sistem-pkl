<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PokjaGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all users in this Pokja group
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'pokja_group_user')
            ->withTimestamps();
    }

    /**
     * Check if a user is member of this group
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is member of this group by user ID
     */
    public function hasMemberId($userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Get total members in this group
     */
    public function getTotalMembersAttribute(): int
    {
        return $this->users()->count();
    }

    /**
     * Scope: Get active groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
