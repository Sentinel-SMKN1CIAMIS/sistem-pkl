<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanInteraktifController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        return view('panduan.interaktif', compact('user', 'role'));
    }
}
