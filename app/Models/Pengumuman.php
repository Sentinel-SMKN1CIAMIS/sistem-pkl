<?php

namespace App\Models;

use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Pengumuman extends Model
{
    use AuditLog;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi',
        'target_role',
        'program_keahlian_id',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Fetch active announcements relevant to a given authenticated user.
     */
    public static function activeForUser(User $user): Collection
    {
        $role = $user->activeWorkspace();

        $query = static::with('programKeahlian:id,kode,nama')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });

        if ($role === 'siswa') {
            $programId = $user->siswa?->konsentrasiKeahlian?->program_keahlian_id;
            $query->whereIn('target_role', ['semua', 'siswa'])
                ->where(function ($q) use ($programId) {
                    $q->whereNull('program_keahlian_id');
                    if ($programId) {
                        $q->orWhere('program_keahlian_id', $programId);
                    }
                });
        } elseif ($role === 'pembimbing_sekolah') {
            $programId = $user->pembimbingSekolah?->konsentrasiKeahlian?->program_keahlian_id;
            $query->whereIn('target_role', ['semua', 'pembimbing_sekolah'])
                ->where(function ($q) use ($programId) {
                    $q->whereNull('program_keahlian_id');
                    if ($programId) {
                        $q->orWhere('program_keahlian_id', $programId);
                    }
                });
        } else {
            return collect();
        }

        return $query->latest('id')->limit(3)->get();
    }
}
