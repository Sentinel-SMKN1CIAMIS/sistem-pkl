<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KompetensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compentencies = \App\Models\Kompetensi::with('konsentrasiKeahlian')->latest()->paginate(10);
        return view('admin.kompetensi.index', compact('compentencies'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        return view('admin.kompetensi.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'kategori' => 'nullable|string|max:100',
        ]);

        \App\Models\Kompetensi::create($request->all());

        return redirect()->route('admin.kompetensi.index')
            ->with('success', 'Elemen Kompetensi berhasil ditambahkan.');
    }

    public function edit(\App\Models\Kompetensi $kompetensi)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        return view('admin.kompetensi.edit', compact('kompetensi', 'concentrations'));
    }

    public function update(Request $request, \App\Models\Kompetensi $kompetensi)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'kategori' => 'nullable|string|max:100',
        ]);

        $kompetensi->update($request->all());

        return redirect()->route('admin.kompetensi.index')
            ->with('success', 'Data kompetensi berhasil diperbarui.');
    }

    public function destroy(\App\Models\Kompetensi $kompetensi)
    {
        $kompetensi->delete();

        return redirect()->route('admin.kompetensi.index')
            ->with('success', 'Data kompetensi berhasil dihapus.');
    }
}
