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
        
        $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingDudi', 'konsentrasiKeahlian'])
            ->when($search, function($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSiswa = Siswa::count();
        $terpetakan = Siswa::whereNotNull('dudi_id')
            ->whereNotNull('pembimbing_sekolah_id')
            ->whereNotNull('pembimbing_dudi_id')
            ->count();
        
        return view('pokja.pemetaan.index', compact('siswas', 'totalSiswa', 'terpetakan'));
    }

    /**
     * Show the interactive map dashboard
     */
    public function maps()
    {
        $totalDudi = Dudi::count();
        $dudiWithCoords = Dudi::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $totalZona = Zona::count();
        $totalSiswa = Siswa::whereNotNull('dudi_id')->count();

        return view('pokja.pemetaan.maps', compact('totalDudi', 'dudiWithCoords', 'totalZona', 'totalSiswa'));
    }

    /**
     * Return DUDI data as JSON for map markers
     */
    public function mapsData()
    {
        $dudis = Dudi::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['siswa.konsentrasiKeahlian', 'zona', 'konsentrasiKeahlians'])
            ->get();

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
