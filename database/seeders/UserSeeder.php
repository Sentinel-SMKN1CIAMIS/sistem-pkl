<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\Dudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => $password,
            'role' => 'super_admin',
        ]);
    }
}
