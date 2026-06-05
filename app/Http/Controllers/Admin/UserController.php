<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $query = User::latest();
        
        // Filter berdasarkan role
        if ($filter === 'super_admin') {
            $query->where('role', 'super_admin');
        } elseif ($filter === 'guru') {
            $query->whereIn('role', ['pembimbing_sekolah', 'pembimbing_dudi']);
        } elseif ($filter === 'siswa') {
            $query->where('role', 'siswa');
        } elseif ($filter === 'other') {
            $query->whereIn('role', ['pokja', 'kaprog']);
        }
        
        $users = $query->paginate(15);
        
        return view('admin.users.index', compact('users', 'filter'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'role' => 'required|in:siswa,pembimbing_sekolah,pembimbing_dudi,pokja,super_admin',
            'password' => 'required|string|min:8|confirmed'
        ]);

        User::create([
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'name' => $request->username, // Default name to username
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role' => 'required|in:siswa,pembimbing_sekolah,pembimbing_dudi,pokja,super_admin',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $user->username = $request->username;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}

