<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pembimbing = $user->pembimbingDudi;
        return view('pembimbing_dudi.profile', compact('user', 'pembimbing'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $pembimbing = $user->pembimbingDudi;

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'jabatan' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $user->name = $request->nama_lengkap;
            $user->username = $request->username;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Update PembimbingDudi
            $pembimbing->update([
                'nama_lengkap' => $request->nama_lengkap,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
            ]);

            DB::commit();
            return back()->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memperbarui profil: ' . $e->getMessage())->withInput();
        }
    }
}
