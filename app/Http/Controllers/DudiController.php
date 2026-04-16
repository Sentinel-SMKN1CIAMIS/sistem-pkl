<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dudis = \App\Models\Dudi::with('konsentrasiKeahlian')->latest()->paginate(10);
        return view('pokja.dudi.index', compact('dudis'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        return view('pokja.dudi.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'nama_pimpinan' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
        ]);

        \App\Models\Dudi::create($request->all());

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
        return view('pokja.dudi.edit', compact('dudi', 'concentrations'));
    }

    public function update(Request $request, \App\Models\Dudi $dudi)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'nama_pimpinan' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
        ]);

        $dudi->update($request->all());

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
