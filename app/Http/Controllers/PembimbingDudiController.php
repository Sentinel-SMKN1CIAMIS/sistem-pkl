<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembimbingDudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentors = \App\Models\PembimbingDudi::with(['user', 'dudi'])->latest()->paginate(10);
        return view('pokja.pembimbing-dudi.index', compact('mentors'));
    }

    public function create()
    {
        $dudis = \App\Models\Dudi::all();
        return view('pokja.pembimbing-dudi.create', compact('dudis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'dudi_id' => 'required|exists:dudis,id',
            'no_hp' => 'nullable|string',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'pembimbing_dudi',
        ]);

        \App\Models\PembimbingDudi::create(array_merge($request->all(), ['user_id' => $user->id]));

        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Pembimbing DUDI berhasil ditambahkan.');
    }

    public function edit(\App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $dudis = \App\Models\Dudi::all();
        return view('pokja.pembimbing-dudi.edit', compact('pembimbing_dudi', 'dudis'));
    }

    public function update(Request $request, \App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'dudi_id' => 'required|exists:dudis,id',
            'no_hp' => 'nullable|string',
        ]);

        $pembimbing_dudi->update($request->all());
        $pembimbing_dudi->user->update(['name' => $request->nama_lengkap]);

        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Data pembimbing DUDI berhasil diperbarui.');
    }

    public function destroy(\App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $pembimbing_dudi->delete();
        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Data pembimbing DUDI berhasil dihapus.');
    }
}
