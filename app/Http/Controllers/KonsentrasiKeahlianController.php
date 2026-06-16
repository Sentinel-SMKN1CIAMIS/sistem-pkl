<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KonsentrasiKeahlianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::with('programKeahlian')->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.konsentrasi-keahlian.index', compact('concentrations'));
    }

    public function create()
    {
        $programs = \App\Models\ProgramKeahlian::all();
        return view('admin.konsentrasi-keahlian.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
            'kode' => 'required|unique:konsentrasi_keahlians,kode',
            'nama' => 'required|string|max:255',
            'durasi_pkl_bulan' => 'required|integer|min:1',
            'tanggal_mulai_pkl' => 'nullable|date',
            'tanggal_selesai_pkl' => 'nullable|date',
        ]);

        \App\Models\KonsentrasiKeahlian::create($request->all());

        return redirect()->route('admin.konsentrasi_keahlian.index')
            ->with('success', 'Konsentrasi Keahlian berhasil ditambahkan.');
    }

    public function show(\App\Models\KonsentrasiKeahlian $konsentrasi_keahlian)
    {
        return view('admin.konsentrasi-keahlian.show', compact('konsentrasi_keahlian'));
    }

    public function edit(\App\Models\KonsentrasiKeahlian $konsentrasi_keahlian)
    {
        $programs = \App\Models\ProgramKeahlian::all();
        return view('admin.konsentrasi-keahlian.edit', compact('konsentrasi_keahlian', 'programs'));
    }

    public function update(Request $request, \App\Models\KonsentrasiKeahlian $konsentrasi_keahlian)
    {
        $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
            'kode' => 'required|unique:konsentrasi_keahlians,kode,' . $konsentrasi_keahlian->id,
            'nama' => 'required|string|max:255',
            'durasi_pkl_bulan' => 'required|integer|min:1',
            'tanggal_mulai_pkl' => 'nullable|date',
            'tanggal_selesai_pkl' => 'nullable|date',
        ]);

        $konsentrasi_keahlian->update($request->all());

        return redirect()->route('admin.konsentrasi_keahlian.index')
            ->with('success', 'Konsentrasi Keahlian berhasil diperbarui.');
    }

    public function destroy(\App\Models\KonsentrasiKeahlian $konsentrasi_keahlian)
    {
        $konsentrasi_keahlian->delete();

        return redirect()->route('admin.konsentrasi_keahlian.index')
            ->with('success', 'Konsentrasi Keahlian berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:konsentrasi_keahlians,id',
        ]);

        $ids = $request->input('ids');
        foreach ($ids as $index => $id) {
            \App\Models\KonsentrasiKeahlian::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Urutan konsentrasi keahlian berhasil diperbarui.',
        ]);
    }
}
