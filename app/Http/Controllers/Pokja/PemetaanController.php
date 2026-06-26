<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\Zona;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use Illuminate\Http\Request;

class PemetaanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $perPage = $request->input('per_page', 15);
        
        $query = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum', 'pembimbingDudi', 'konsentrasiKeahlian']);
        
        if (auth()->user()->konsentrasi_keahlian_id) {
            $query->where('konsentrasi_keahlian_id', auth()->user()->konsentrasi_keahlian_id);
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
        }

        $konsentrasi_id = $request->input('konsentrasi_id', 'semua');
        $status = $request->input('status', 'semua');

        $siswas = $query->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($status === 'lengkap', function($query) {
                $query->whereNotNull('dudi_id')
                      ->whereNotNull('pembimbing_sekolah_id')
                      ->whereNotNull('pembimbing_sekolah_umum_id')
                      ->whereNotNull('pembimbing_dudi_id');
            })
            ->when($status === 'belum-lengkap', function($query) {
                $query->where(function($q) {
                    $q->whereNull('dudi_id')
                      ->orWhereNull('pembimbing_sekolah_id')
                      ->orWhereNull('pembimbing_sekolah_umum_id')
                      ->orWhereNull('pembimbing_dudi_id');
                });
            })
            ->when($konsentrasi_id !== 'semua', function($query) use ($konsentrasi_id) {
                $query->where('konsentrasi_keahlian_id', $konsentrasi_id);
            })
            ->when($status === 'belum-lengkap', function($query) {
                $query->where(function($q) {
                    $q->whereNull('dudi_id')
                      ->orWhereNull('pembimbing_sekolah_id')
                      ->orWhereNull('pembimbing_sekolah_umum_id')
                      ->orWhereNull('pembimbing_dudi_id');
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $baseSiswaQuery = Siswa::query();
        if (auth()->user()->konsentrasi_keahlian_id) {
            $baseSiswaQuery->where('konsentrasi_keahlian_id', auth()->user()->konsentrasi_keahlian_id);
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $baseSiswaQuery->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
        }
        
        $totalSiswa = (clone $baseSiswaQuery)->count();
        $terpetakan = (clone $baseSiswaQuery)->whereNotNull('dudi_id')
            ->whereNotNull('pembimbing_sekolah_id')
            ->whereNotNull('pembimbing_sekolah_umum_id')
            ->whereNotNull('pembimbing_dudi_id')
            ->count();
        
        $konsentrasiQuery = \App\Models\KonsentrasiKeahlian::query();
        if (auth()->user()->konsentrasi_keahlian_id) {
            $konsentrasiQuery->where('id', auth()->user()->konsentrasi_keahlian_id);
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $konsentrasiQuery->whereIn('id', $konsentrasiIds);
        }
        $konsentrasiList = $konsentrasiQuery->orderBy('kode')->get(['id', 'kode']);

        return view('pokja.pemetaan.index', compact('siswas', 'totalSiswa', 'terpetakan', 'perPage', 'konsentrasiList'));
    }

    /**
     * Show the interactive map dashboard
     */
    public function maps()
    {
        $dudiQuery = Dudi::query();
        $siswaQuery = Siswa::query();
        $user = auth()->user();

        if ($user->role === 'pembimbing_sekolah') {
            $pembimbing = $user->pembimbingSekolah;
            if ($pembimbing) {
                $siswaQuery->where(function($q) use ($pembimbing) {
                    $q->where('pembimbing_sekolah_id', $pembimbing->id)
                      ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
                });
                $dudiQuery->whereHas('siswa', function($q) use ($pembimbing) {
                    $q->where('pembimbing_sekolah_id', $pembimbing->id)
                      ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
                });
            } else {
                $siswaQuery->whereNull('id');
                $dudiQuery->whereNull('id');
            }
        } else {
            if ($user->konsentrasi_keahlian_id) {
                $userKonId = $user->konsentrasi_keahlian_id;
                $dudiQuery->where(function($q) use ($userKonId) {
                    $q->where('konsentrasi_keahlian_id', $userKonId)
                      ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                          $sub->where('konsentrasi_keahlians.id', $userKonId);
                      });
                });
                $siswaQuery->where('konsentrasi_keahlian_id', $userKonId);
            } elseif ($user->program_keahlian_id) {
                $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id');
                $dudiQuery->where(function($q) use ($konsentrasiIds) {
                    $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                      ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                          $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                      });
                });
                $siswaQuery->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
            }
        }

        $totalDudi = (clone $dudiQuery)->count();
        $dudiWithCoords = (clone $dudiQuery)->whereNotNull('latitude')->whereNotNull('longitude')->count();
        $totalZona = Zona::count();
        $totalSiswa = (clone $siswaQuery)->whereNotNull('dudi_id')->count();

        return view('pokja.pemetaan.maps', compact('totalDudi', 'dudiWithCoords', 'totalZona', 'totalSiswa'));
    }

    /**
     * Return DUDI data as JSON for map markers
     */
    public function mapsData()
    {
        $dudiQuery = Dudi::whereNotNull('latitude')->whereNotNull('longitude');
        $user = auth()->user();

        if ($user->role === 'pembimbing_sekolah') {
            $pembimbing = $user->pembimbingSekolah;
            if ($pembimbing) {
                $dudiQuery->whereHas('siswa', function($q) use ($pembimbing) {
                    $q->where('pembimbing_sekolah_id', $pembimbing->id)
                      ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
                });
                
                $dudis = $dudiQuery->with([
                    'siswa' => function($q) use ($pembimbing) {
                        $q->where('pembimbing_sekolah_id', $pembimbing->id)
                          ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id)
                          ->with('konsentrasiKeahlian');
                    },
                    'zona',
                    'konsentrasiKeahlians'
                ])->get();
            } else {
                $dudis = collect();
            }
        } else {
            if ($user->konsentrasi_keahlian_id) {
                $userKonId = $user->konsentrasi_keahlian_id;
                $dudiQuery->where(function($q) use ($userKonId) {
                    $q->where('konsentrasi_keahlian_id', $userKonId)
                      ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                          $sub->where('konsentrasi_keahlians.id', $userKonId);
                      });
                });
            } elseif ($user->program_keahlian_id) {
                $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id');
                $dudiQuery->where(function($q) use ($konsentrasiIds) {
                    $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                      ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                          $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                      });
                });
            }

            $dudis = $dudiQuery->with(['siswa.konsentrasiKeahlian', 'zona', 'konsentrasiKeahlians'])->get();
        }

        $markers = $dudis->map(function ($dudi) {
            // Group students by konsentrasi keahlian
            $jurusanCounts = $dudi->siswa->groupBy(function($s) {
                return $s->konsentrasiKeahlian ? $s->konsentrasiKeahlian->nama : 'Tidak Diketahui';
            })->map->count();

            return [
                'id' => $dudi->id,
                'nama' => $dudi->nama,
                'alamat' => $dudi->alamat,
                'kota' => $dudi->kota,
                'lat' => (float) $dudi->latitude,
                'lng' => (float) $dudi->longitude,
                'jenis_industri' => $dudi->jenis_industri ?? 'lainnya',
                'nama_pimpinan' => $dudi->nama_pimpinan,
                'no_telepon' => $dudi->no_telepon,
                'bidang_usaha' => $dudi->bidang_usaha,
                'zona' => $dudi->zona ? $dudi->zona->nama : null,
                'total_siswa' => $dudi->siswa->count(),
                'jurusan' => $jurusanCounts,
                'siswa_list' => $dudi->siswa->map(fn($s) => [
                    'nama' => $s->nama_lengkap,
                    'nis' => $s->nis,
                    'jurusan' => $s->konsentrasiKeahlian ? $s->konsentrasiKeahlian->nama : '-',
                ]),
            ];
        });

        $zonas = Zona::all()->map(function ($zona) {
            return [
                'id' => $zona->id,
                'nama' => $zona->nama,
                'warna' => $zona->warna,
                'warna_border' => $zona->warna_border,
                'nomor_zona' => $zona->nomor_zona,
                'koordinat' => $zona->koordinat_geojson,
            ];
        });

        return response()->json([
            'markers' => $markers,
            'zonas' => $zonas,
        ]);
    }
}
