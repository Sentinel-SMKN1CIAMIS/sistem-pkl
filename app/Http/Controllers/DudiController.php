<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DudiController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Dudi::with(['konsentrasiKeahlian', 'konsentrasiKeahlians']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%")
                  ->orWhere('bidang_usaha', 'like', "%{$search}%");
            });
        }

        if ($request->filled('konsentrasi')) {
            $query->where(function($q) use ($request) {
                $q->where('konsentrasi_keahlian_id', $request->konsentrasi)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($request) {
                      $sub->where('konsentrasi_keahlians.id', $request->konsentrasi);
                  });
            });
        }

        $dudis = $query->latest()->paginate(10)->withQueryString();
        $concentrations = \App\Models\KonsentrasiKeahlian::all();

        return view('pokja.dudi.index', compact('dudis', 'concentrations'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        return view('pokja.dudi.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konsentrasi_keahlian_ids' => 'required|array|min:1',
            'konsentrasi_keahlian_ids.*' => 'exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'nama_pimpinan' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['konsentrasi_keahlian_id'] = $request->konsentrasi_keahlian_ids[0];

        $dudi = \App\Models\Dudi::create($data);
        $dudi->konsentrasiKeahlians()->sync($request->konsentrasi_keahlian_ids);

        return redirect()->route('pokja.dudi.index')
            ->with('success', 'Data DUDI berhasil ditambahkan.');
    }

    public function show(\App\Models\Dudi $dudi)
    {
        return view('pokja.dudi.show', compact('dudi'));
    }

    public function edit(\App\Models\Dudi $dudi)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $selectedConcentrationIds = $dudi->konsentrasiKeahlians->pluck('id')->toArray();
        if (empty($selectedConcentrationIds)) {
            $selectedConcentrationIds = [$dudi->konsentrasi_keahlian_id];
        }
        return view('pokja.dudi.edit', compact('dudi', 'concentrations', 'selectedConcentrationIds'));
    }

    public function update(Request $request, \App\Models\Dudi $dudi)
    {
        $request->validate([
            'konsentrasi_keahlian_ids' => 'required|array|min:1',
            'konsentrasi_keahlian_ids.*' => 'exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'nama_pimpinan' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        $data['konsentrasi_keahlian_id'] = $request->konsentrasi_keahlian_ids[0];

        // Auto-detect zona if coordinates are provided
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $zona = \App\Models\Zona::detectZona((float) $request->latitude, (float) $request->longitude);
            $data['zona_id'] = $zona ? $zona->id : null;
        } else {
            $data['zona_id'] = null; // Clear zona if coords are removed
        }

        $dudi->update($data);
        $dudi->konsentrasiKeahlians()->sync($request->konsentrasi_keahlian_ids);

        return redirect()->route('pokja.dudi.index')
            ->with('success', 'Data DUDI berhasil diperbarui.');
    }

    public function destroy(\App\Models\Dudi $dudi)
    {
        $dudi->delete();

        return redirect()->route('pokja.dudi.index')
            ->with('success', 'Data DUDI berhasil dihapus.');
    }
}
