<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PembimbingDudi;
use App\Models\Dudi;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterPembimbingDudiController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $dudis = Dudi::where('is_active', true)->orderBy('nama')->get();
        return view('auth.register-pembimbing-dudi', compact('dudis'));
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        // Normalize phone number to 08... format
        if ($request->filled('no_hp')) {
            $clean_no_hp = str_replace([' ', '-', '+'], '', $request->no_hp);
            if (str_starts_with($clean_no_hp, '62')) {
                $clean_no_hp = '0' . substr($clean_no_hp, 2);
            } elseif (!str_starts_with($clean_no_hp, '0') && $clean_no_hp !== '') {
                $clean_no_hp = '0' . $clean_no_hp;
            }
            $request->merge(['no_hp' => $clean_no_hp]);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#\-_])[a-zA-Z\d@$!%*?&#\-_]+$/',
                'confirmed'
            ],
            'jabatan' => 'nullable|string|max:100',
            'no_hp' => [
                'required',
                'string',
                'regex:/^0[0-9]{8,13}$/',
            ],
            'dudi_id' => 'required|exists:dudis,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah terdaftar.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password harus minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial (@$!%*?&#-_).',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Harus diawali dengan angka 0 atau +62 dan berisi 9-14 digit angka.',
            'dudi_id.required' => 'Perusahaan wajib dipilih.',
            'dudi_id.exists' => 'Perusahaan yang dipilih tidak valid.',
            'latitude.required' => 'Koordinat latitude harus ditentukan di peta.',
            'longitude.required' => 'Koordinat longitude harus ditentukan di peta.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // 1. Create User
            $user = User::create([
                'name' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pembimbing_dudi',
                'is_active' => true,
            ]);

            // 2. Create PembimbingDudi
            PembimbingDudi::create([
                'user_id' => $user->id,
                'dudi_id' => $request->dudi_id,
                'nama_lengkap' => $request->nama_lengkap,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
            ]);

            // 3. Update Dudi coordinate & zone
            $dudi = Dudi::find($request->dudi_id);
            $zona = Zona::detectZona($request->latitude, $request->longitude);
            $dudi->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'zona_id' => $zona ? $zona->id : null,
            ]);

            DB::commit();

            // 4. Log in immediately
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Anda telah masuk secara otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mendaftar: ' . $e->getMessage())->withInput();
        }
    }
}
