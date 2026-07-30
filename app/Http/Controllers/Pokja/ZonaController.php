<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use App\Models\Dudi;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $zonas = Zona::withCount('dudis')->orderBy('nomor_zona')->paginate($perPage)->withQueryString();
        return view('pokja.zona.index', compact('zonas', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'warna' => 'required|string|max:7',
            'warna_border' => 'required|string|max:7',
            'koordinat_geojson' => 'required|json',
            'nomor_zona' => 'nullable|integer',
        ]);

        $zona = Zona::create([
            'nama' => $request->nama,
            'warna' => $request->warna,
            'warna_border' => $request->warna_border,
            'koordinat_geojson' => json_decode($request->koordinat_geojson, true),
            'nomor_zona' => $request->nomor_zona,
        ]);

        // Re-assign DUDIs that fall within this new zone
        $this->reassignDudisToZona($zona);

        return response()->json(['message' => 'Zona berhasil disimpan.', 'zona' => $zona]);
    }

    public function update(Request $request, Zona $zona)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'warna' => 'required|string|max:7',
            'warna_border' => 'required|string|max:7',
            'koordinat_geojson' => 'required|json',
            'nomor_zona' => 'nullable|integer',
        ]);

        $zona->update([
            'nama' => $request->nama,
            'warna' => $request->warna,
            'warna_border' => $request->warna_border,
            'koordinat_geojson' => json_decode($request->koordinat_geojson, true),
            'nomor_zona' => $request->nomor_zona,
        ]);

        // Re-assign DUDIs
        $this->reassignDudisToZona($zona);

        return response()->json(['message' => 'Zona berhasil diperbarui.', 'zona' => $zona]);
    }

    public function destroy(Zona $zona)
    {
        // Unset zona_id for all DUDIs in this zona
        Dudi::where('zona_id', $zona->id)->update(['zona_id' => null]);
        $zona->delete();

        return response()->json(['message' => 'Zona berhasil dihapus.']);
    }

    /**
     * Return all zonas as GeoJSON for map rendering
     */
    public function geojson()
    {
        $zonas = Zona::all();
        return response()->json($zonas);
    }

    /**
     * Re-assign DUDIs that have coordinates to this zona if they fall inside the polygon.
     */
    private function reassignDudisToZona(Zona $zona): void
    {
        $dudis = Dudi::whereNotNull('latitude')->whereNotNull('longitude')->get();
        foreach ($dudis as $dudi) {
            $detected = Zona::detectZona($dudi->latitude, $dudi->longitude);
            $dudi->zona_id = $detected ? $detected->id : null;
            $dudi->saveQuietly();
        }
    }
}
