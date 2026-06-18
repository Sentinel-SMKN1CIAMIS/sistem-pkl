<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\AuditLog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'avatar', 'is_active', 'last_login_at', 'force_password_change', 'konsentrasi_keahlian_id', 'program_keahlian_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, AuditLog;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'force_password_change' => 'boolean',
        ];
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function pembimbingSekolah()
    {
        return $this->hasOne(PembimbingSekolah::class);
    }

    public function pembimbingDudi()
    {
        return $this->hasOne(PembimbingDudi::class);
    }

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function kaprog()
    {
        return $this->hasOne(Kaprog::class);
    }

    public function pokjaGroups()
    {
        return $this->belongsToMany(PokjaGroup::class, 'pokja_group_user')
            ->withTimestamps();
    }

    /**
     * Check if user is member of any active Pokja group
     */
    public function hasActivePokjaGroup(): bool
    {
        return $this->pokjaGroups()
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get the active Pokja group for this user (if member of one)
     */
    public function getActivePokjaGroup()
    {
        return $this->pokjaGroups()
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get filtered Konsentrasi Keahlian based on user's role/scope
     */
    public function getFilteredKonsentrasi()
    {
        $query = KonsentrasiKeahlian::query();
        if ($this->konsentrasi_keahlian_id) {
            $query->where('id', $this->konsentrasi_keahlian_id);
        } elseif ($this->program_keahlian_id) {
            $query->where('program_keahlian_id', $this->program_keahlian_id);
        }
        return $query->get();
    }
}
