<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaprogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'kaprog')->with('programKeahlian');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program')) {
            $query->where('program_keahlian_id', $request->program);
        }

        $kaprogs = $query->latest()->paginate(10)->withQueryString();
        $programs = ProgramKeahlian::all();

        return view('pokja.kaprog.index', compact('kaprogs', 'programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = ProgramKeahlian::all();
        return view('pokja.kaprog.create', compact('programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kaprog',
            'program_keahlian_id' => $request->program_keahlian_id,
            'is_active' => true,
        ]);

        return redirect()->route('pokja.kaprog.index')
            ->with('success', 'Akun Kaprog berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $kaprog)
    {
        // Ensure we only edit a kaprog role user
        if ($kaprog->role !== 'kaprog') {
            abort(404);
        }

        $programs = ProgramKeahlian::all();
        return view('pokja.kaprog.edit', compact('kaprog', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $kaprog)
    {
        if ($kaprog->role !== 'kaprog') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:50|unique:users,username,' . $kaprog->id,
            'email' => 'required|email|unique:users,email,' . $kaprog->id,
            'password' => 'nullable|string|min:6',
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
        ]);

        $kaprog->name = $request->name;
        $kaprog->username = $request->username;
        $kaprog->email = $request->email;
        $kaprog->program_keahlian_id = $request->program_keahlian_id;

        if ($request->filled('password')) {
            $kaprog->password = Hash::make($request->password);
        }

        $kaprog->save();

        return redirect()->route('pokja.kaprog.index')
            ->with('success', 'Akun Kaprog berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $kaprog)
    {
        if ($kaprog->role !== 'kaprog') {
            abort(404);
        }

        $kaprog->delete();

        return redirect()->route('pokja.kaprog.index')
            ->with('success', 'Akun Kaprog berhasil dihapus.');
    }
}
