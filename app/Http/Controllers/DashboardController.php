<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        
        switch ($role) {
            case 'siswa':
                return view('dashboards.siswa');
            case 'pembimbing_sekolah':
                return view('dashboards.pembimbing-sekolah');
            case 'pembimbing_dudi':
                return view('dashboards.pembimbing-dudi');
            case 'pokja':
                return view('dashboards.pokja');
            case 'super_admin':
                return view('dashboards.super-admin');
            default:
                abort(403, 'Unauthorized action.');
        }
    }
}
