<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        // Get all school mentors with their student counts and journal validation stats
        $mentors = PembimbingSekolah::withCount('siswa')->latest()->get();
        
        return view('pokja.monitoring.index', compact('mentors'));
    }
}
