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
        $query = \App\Models\PembimbingDudi::with(['user', 'dudi'])->latest();
        if (auth()->user()->konsentrasi_keahlian_id) {
            $userKonId = auth()->user()->konsentrasi_keahlian_id;
            $query->whereHas('dudi', function($q) use ($userKonId) {
                $q->where('konsentrasi_keahlian_id', $userKonId)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                      $sub->where('konsentrasi_keahlians.id', $userKonId);
                  });
            });
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereHas('dudi', function($q) use ($konsentrasiIds) {
                $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                      $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                  });
            });
        }
        $mentors = $query->paginate(10);
        return view('pokja.pembimbing-dudi.index', compact('mentors'));
    }

    public function create()
    {
        $dudis = \App\Models\Dudi::all();
        $manualMentors = \App\Models\Siswa::whereNotNull('pembimbing_dudi_nama')
            ->whereNull('pembimbing_dudi_id')
            ->with('dudi')
            ->get();
        return view('pokja.pembimbing-dudi.create', compact('dudis', 'manualMentors'));
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
            'siswa_id' => 'nullable|exists:siswas,id',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'pembimbing_dudi',
        ]);

        $pembimbingDudi = \App\Models\PembimbingDudi::create(array_merge($request->all(), ['user_id' => $user->id]));

        if ($request->filled('siswa_id')) {
            $siswa = \App\Models\Siswa::find($request->siswa_id);
            if ($siswa) {
                $siswa->update([
                    'pembimbing_dudi_id' => $pembimbingDudi->id
                ]);
            }
        }

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
        $user = $pembimbing_dudi->user;
        $pembimbing_dudi->delete();
        if ($user) {
            $user->delete();
        }
        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Data pembimbing DUDI berhasil dihapus.');
    }
}
