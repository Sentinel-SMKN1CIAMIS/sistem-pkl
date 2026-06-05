<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Only siswa role can access change password
        if ($user->role !== 'siswa') {
            return redirect()->route('dashboard');
        }
        
        // Check if user needs to change password
        if (!$user->force_password_change) {
            return redirect()->intended('dashboard');
        }

        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Only siswa role can change password through this route
        if ($user->role !== 'siswa') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard')->withErrors(['error' => 'Anda tidak memiliki akses ke fitur ini.']);
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]/',
                'confirmed'
            ],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password harus minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial (@$!%*?&).',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            // Update password and clear the force_password_change flag
            $user->update([
                'password' => Hash::make($validated['password']),
                'force_password_change' => false,
            ]);

            // Log the password change activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Password Changed',
                'description' => 'Siswa mengubah password pada login pertama kali',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Regenerate session after password change for security
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json(['success' => 'Password berhasil diubah']);
            }

            return redirect()->intended('dashboard')->with('success', 'Password berhasil diubah. Selamat datang di dashboard!');
        } catch (\Exception $e) {
            // Log failed password change attempt
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Password Change Failed',
                'description' => 'Gagal mengubah password: ' . $e->getMessage(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan saat mengubah password'], 500);
            }

            return back()->withErrors([
                'password' => 'Terjadi kesalahan saat mengubah password. Silakan coba lagi.',
            ]);
        }
    }
}
