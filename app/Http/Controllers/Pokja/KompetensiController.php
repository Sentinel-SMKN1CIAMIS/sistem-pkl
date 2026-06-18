<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;

class KompetensiController extends Controller
{
    public function index()
    {
        $compentencies = Kompetensi::with('konsentrasiKeahlian')->latest()->paginate(10);
        return view('pokja.kompetensi.index', compact('compentencies'));
    }

    public function create()
    {
        $concentrations = KonsentrasiKeahlian::all();
        return view('pokja.kompetensi.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'tp' => 'nullable|string|max:500',
            'cp' => 'nullable|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        Kompetensi::create($request->all());

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil ditambahkan.');
    }

    public function edit(Kompetensi $kompetensi)
    {
        $concentrations = KonsentrasiKeahlian::all();
        return view('pokja.kompetensi.edit', compact('kompetensi', 'concentrations'));
    }

    public function update(Request $request, Kompetensi $kompetensi)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'tp' => 'nullable|string|max:500',
            'cp' => 'nullable|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        $kompetensi->update($request->all());

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil diperbarui.');
    }

    public function destroy(Kompetensi $kompetensi)
    {
        $kompetensi->delete();

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil dihapus.');
    }
}
