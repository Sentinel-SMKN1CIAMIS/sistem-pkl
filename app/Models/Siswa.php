<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'konsentrasi_keahlian_id', 'dudi_id', 'pembimbing_sekolah_id', 'pembimbing_sekolah_umum_id', 'pembimbing_dudi_id', 'nis', 'nama_lengkap', 'kelas', 'jenis_kelamin', 'no_hp', 'alamat', 'tahun_ajaran', 'status_pkl', 'pembimbing_dudi_nama', 'pembimbing_dudi_jabatan', 'unit_pekerjaan'])]
class Siswa extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($siswa) {
            if ($siswa->dudi_id && ($siswa->pembimbing_sekolah_id || $siswa->pembimbing_sekolah_umum_id) && $siswa->status_pkl === 'belum_mulai') {
                $siswa->status_pkl = 'sedang_pkl';
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function pembimbingSekolah()
    {
        return $this->belongsTo(PembimbingSekolah::class);
    }

    public function pembimbingSekolahUmum()
    {
        return $this->belongsTo(PembimbingSekolah::class, 'pembimbing_sekolah_umum_id');
    }

    public function pembimbingDudi()
    {
        return $this->belongsTo(PembimbingDudi::class);
    }

    public function jurnal()
    {
        return $this->hasMany(Jurnal::class);
    }

    public function pengajuanPkl()
    {
        return $this->hasOne(PengajuanPkl::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function laporan()
    {
        return $this->hasOne(LaporanPkl::class);
    }

    public function getStatusHariIniAttribute()
    {
        if ($this->status_pkl !== 'sedang_pkl') {
            return str_replace('_', ' ', $this->status_pkl);
        }

        $todayAbsensi = Absensi::where('siswa_id', $this->id)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->first();

        if ($todayAbsensi) {
            if (in_array($todayAbsensi->status, ['sakit', 'izin', 'alpha'])) {
                return $todayAbsensi->status;
            } elseif ($todayAbsensi->waktu_pulang) {
                return 'pulang kerja';
            } else {
                return 'masuk kerja';
            }
        }

        return 'belum absen';
    }
}

