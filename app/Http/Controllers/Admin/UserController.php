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
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $perPage = $request->get('per_page', 15);
        
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }
        
        $allowedSorts = ['created_at', 'username', 'role', 'name'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';
        
        $query = User::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan role
        if ($filter === 'super_admin') {
            $query->where('role', 'super_admin');
        } elseif ($filter === 'guru') {
            $query->whereIn('role', ['pembimbing_sekolah', 'pembimbing_dudi']);
        } elseif ($filter === 'siswa') {
            $query->where('role', 'siswa');
        } elseif ($filter === 'pokja') {
            $query->where('role', 'pokja');
        } elseif ($filter === 'kaprog') {
            $query->where('role', 'kaprog');
        } elseif ($filter === 'kepala_sekolah') {
            $query->where('role', 'kepala_sekolah');
        }
        
        $query->orderBy($sortBy, $sortDir);
        
        $users = $query->paginate($perPage)->withQueryString();
        
        return view('admin.users.index', compact('users', 'filter', 'perPage'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'role' => 'required|in:siswa,pembimbing_sekolah,pembimbing_dudi,pokja,super_admin,kepala_sekolah,kaprog',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($request->role === 'kepala_sekolah' && User::where('role', 'kepala_sekolah')->exists()) {
            return back()->withInput()->withErrors(['role' => 'Akun dengan role Kepala Sekolah sudah ada. Maksimal hanya diperbolehkan 1 akun.']);
        }

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
            'role' => 'required|in:siswa,pembimbing_sekolah,pembimbing_dudi,pokja,super_admin,kepala_sekolah,kaprog',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        if ($request->role === 'kepala_sekolah' && $user->role !== 'kepala_sekolah' && User::where('role', 'kepala_sekolah')->exists()) {
            return back()->withInput()->withErrors(['role' => 'Akun dengan role Kepala Sekolah sudah ada. Maksimal hanya diperbolehkan 1 akun.']);
        }

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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:users,id',
        ]);

        $ids = $request->input('ids');
        
        // Exclude the current authenticated user's ID
        $filteredIds = array_filter($ids, function($id) {
            return $id != auth()->id();
        });

        if (empty($filteredIds)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak ada akun valid yang dapat dihapus.');
        }

        User::whereIn('id', $filteredIds)->delete();

        return back()
            ->with('success', count($filteredIds) . ' akun berhasil dihapus.');
    }
}

