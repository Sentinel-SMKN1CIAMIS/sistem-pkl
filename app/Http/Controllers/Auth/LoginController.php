<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($request->input('username'));
        $password = (string) $request->input('password');

        // 1. Cari user secara fleksibel: Username, Email, Nama Lengkap
        $user = \App\Models\User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->first();

        // Fallback pencarian berbasis NIP Guru Pembimbing Sekolah
        if (!$user) {
            $cleanNip = preg_replace('/\D/', '', $loginInput);
            $pembimbing = \App\Models\PembimbingSekolah::where('nip', $loginInput)
                ->when(!empty($cleanNip), fn($q) => $q->orWhere('nip', $cleanNip))
                ->first();

            if (!$pembimbing && strlen($cleanNip) >= 8) {
                // Cari jika di DB NIP tersimpan dengan spasi atau tanda baca
                $pembimbing = \App\Models\PembimbingSekolah::whereNotNull('nip')
                    ->where('nip', '!=', '-')
                    ->get()
                    ->first(function ($p) use ($cleanNip) {
                        return preg_replace('/\D/', '', (string) $p->nip) === $cleanNip;
                    });
            }

            if ($pembimbing && $pembimbing->user) {
                $user = $pembimbing->user;
            }
        }

        // Fallback pencarian berbasis NIS atau Nama Lengkap Siswa
        if (!$user) {
            $siswa = \App\Models\Siswa::where('nis', $loginInput)
                ->orWhere('nama_lengkap', $loginInput)
                ->first();
            if ($siswa && $siswa->user) {
                $user = $siswa->user;
            }
        }

        if ($user) {
            $isPasswordValid = \Illuminate\Support\Facades\Hash::check($password, $user->password);

            // Toleransi ramah pengguna jika akun guru masih menggunakan sandi bawaan (force_password_change = true)
            // Mengizinkan guru masuk dengan NIP-nya ATAU 'smkn1ciamis'
            if (!$isPasswordValid && $user->force_password_change && $user->role === 'pembimbing_sekolah') {
                $teacherNip = $user->pembimbingSekolah?->nip;
                $cleanTeacherNip = $teacherNip ? preg_replace('/\D/', '', $teacherNip) : '';
                $cleanPassword = preg_replace('/\D/', '', $password);

                $nipMatches = ($teacherNip && $password === $teacherNip) ||
                              (!empty($cleanTeacherNip) && $cleanPassword === $cleanTeacherNip);

                if (($nipMatches || $password === 'smkn1ciamis') && ($teacherNip || !empty($cleanTeacherNip))) {
                    $targetNip = !empty($cleanTeacherNip) ? $cleanTeacherNip : $teacherNip;
                    if (\Illuminate\Support\Facades\Hash::check('smkn1ciamis', $user->password) ||
                        \Illuminate\Support\Facades\Hash::check($targetNip, $user->password) ||
                        ($teacherNip && \Illuminate\Support\Facades\Hash::check($teacherNip, $user->password))) {
                        $isPasswordValid = true;
                        $user->password = $targetNip;
                        $user->saveQuietly();
                    }
                }
            }

            if ($isPasswordValid) {
                // [SECURITY FIX MED-01] Cek is_active SEBELUM regenerate session dan update last_login_at
                // Akun non-aktif tidak boleh punya sesi valid sama sekali
                if (!$user->is_active) {
                    return back()->withErrors([
                        'username' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                    ]);
                }

                // Cek apakah sistem sedang dalam mode maintenance dan peran pengguna diizinkan
                if (\App\Services\MaintenanceService::isMaintenanceActive() && !\App\Services\MaintenanceService::isRoleAllowed($user->role)) {
                    $roleLabel = \App\Services\MaintenanceService::ALL_ROLES[$user->role]['label'] ?? ucfirst($user->role);
                    return back()->withErrors([
                        'username' => "Sistem sedang dalam mode pemeliharaan (maintenance). Peran {$roleLabel} saat ini belum diberikan izin akses.",
                    ])->onlyInput('username');
                }

                Auth::login($user, $request->boolean('remember'));

                $request->session()->regenerate();
                $user->update(['last_login_at' => now()]);

                // Check if user needs to change password on first login
                if ($user->force_password_change) {
                    return redirect()->route('auth.change-password.show');
                }

                // Dual-role Pembimbing Sekolah + Pokja -> redirect to Workspace Selector
                if ($user->hasDualWorkspace()) {
                    $request->session()->forget('active_workspace');
                    return redirect()->route('workspace.select');
                }

                return redirect()->intended('dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
