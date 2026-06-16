<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Siswa::with(['user', 'konsentrasiKeahlian', 'dudi', 'pembimbingSekolah']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('konsentrasi')) {
            $query->where('konsentrasi_keahlian_id', $request->konsentrasi);
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        
        return view('pokja.siswa.index', compact('students', 'concentrations'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $dudis = \App\Models\Dudi::all();
        $pembimbingSekolah = \App\Models\PembimbingSekolah::all();
        $pembimbingDudi = \App\Models\PembimbingDudi::all();

        return view('pokja.siswa.create', compact('concentrations', 'dudis', 'pembimbingSekolah', 'pembimbingDudi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'kelas' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_ajaran' => 'required|string',
        ]);

        try {
            // Wrap in database transaction for atomicity
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Create User first
            $user = \App\Models\User::create([
                'name' => $request->nama_lengkap,
                'username' => $request->nis,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'siswa',
            ]);

            // Create Siswa profile
            \App\Models\Siswa::create(array_merge($request->all(), ['user_id' => $user->id]));

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pokja.siswa.index')
                ->with('success', 'Data siswa dan akun berhasil dibuat.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            return back()->with('error', 'Gagal menambahkan data siswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(\App\Models\Siswa $siswa)
    {
        return view('pokja.siswa.show', compact('siswa'));
    }

    public function edit(\App\Models\Siswa $siswa)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $dudis = \App\Models\Dudi::all();
        $pembimbingSekolah = \App\Models\PembimbingSekolah::all();
        $pembimbingDudi = \App\Models\PembimbingDudi::all();

        return view('pokja.siswa.edit', compact('siswa', 'concentrations', 'dudis', 'pembimbingSekolah', 'pembimbingDudi'));
    }

    public function update(Request $request, \App\Models\Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            // Email and password usually handled separately or left as is
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'kelas' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_ajaran' => 'required|string',
        ]);

        $siswa->update($request->all());
        
        // Update user name if changed
        $siswa->user->update(['name' => $request->nama_lengkap, 'username' => $request->nis]);

        return redirect()->route('pokja.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(\App\Models\Siswa $siswa)
    {
        $user = $siswa->user;
        $siswa->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('pokja.siswa.index')
            ->with('success', 'Data siswa dan akun berhasil dihapus.');
    }
}
