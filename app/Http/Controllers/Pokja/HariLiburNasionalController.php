<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\HariLiburNasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HariLiburNasionalController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        $holidays = HariLiburNasional::with('creator:id,name')
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('pokja.hari-libur.index', compact('holidays', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'unique:hari_libur_nasionals,tanggal'],
            'nama' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal.unique' => 'Tanggal merah tersebut sudah terdaftar.',
            'nama.required' => 'Nama hari libur wajib diisi.',
        ]);

        HariLiburNasional::create([
            'tanggal' => $validated['tanggal'],
            'nama' => $validated['nama'],
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Tanggal merah nasional berhasil ditambahkan.');
    }

    public function destroy(HariLiburNasional $hariLibur)
    {
        $hariLibur->delete();

        return back()->with('success', 'Tanggal merah nasional berhasil dihapus.');
    }
}
