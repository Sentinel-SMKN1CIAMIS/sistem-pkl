<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramKeahlianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = \App\Models\ProgramKeahlian::latest()->paginate(10);
        return view('admin.program-keahlian.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.program-keahlian.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:program_keahlians,kode',
            'nama' => 'required|string|max:255',
        ]);

        \App\Models\ProgramKeahlian::create($request->all());

        return redirect()->route('admin.program_keahlian.index')
            ->with('success', 'Program Keahlian berhasil ditambahkan.');
    }

    public function show(\App\Models\ProgramKeahlian $program_keahlian)
    {
        return view('admin.program-keahlian.show', compact('program_keahlian'));
    }

    public function edit(\App\Models\ProgramKeahlian $program_keahlian)
    {
        return view('admin.program-keahlian.edit', compact('program_keahlian'));
    }

    public function update(Request $request, \App\Models\ProgramKeahlian $program_keahlian)
    {
        $request->validate([
            'kode' => 'required|unique:program_keahlians,kode,' . $program_keahlian->id,
            'nama' => 'required|string|max:255',
        ]);

        $program_keahlian->update($request->all());

        return redirect()->route('admin.program_keahlian.index')
            ->with('success', 'Program Keahlian berhasil diperbarui.');
    }

    public function destroy(\App\Models\ProgramKeahlian $program_keahlian)
    {
        $program_keahlian->delete();

        return redirect()->route('admin.program_keahlian.index')
            ->with('success', 'Program Keahlian berhasil dihapus.');
    }
}
