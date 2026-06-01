<?php

$siswas = App\Models\Siswa::where('nama_lengkap', 'like', '%pradipta%')
    ->orWhere('nama_lengkap', 'like', '%khawarizmi%')
    ->with('user')
    ->get();

foreach ($siswas as $s) {
    echo "Name: " . $s->nama_lengkap . " | Username: " . ($s->user ? $s->user->username : 'No user') . "\n";
}
