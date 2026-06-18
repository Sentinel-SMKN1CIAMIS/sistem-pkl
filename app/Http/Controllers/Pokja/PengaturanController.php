<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function sertifikat()
    {
        $template = KonfigurasiSistem::where('key', 'template_sertifikat')->value('value') ?? "Diberikan kepada [NAMA_SISWA] atas keberhasilannya menyelesaikan Praktik Kerja Lapangan di [NAMA_DUDI] pada tanggal [TANGGAL_AWAL] s/d [TANGGAL_AKHIR].";
        
        return view('pokja.pengaturan.sertifikat', compact('template'));
    }

    public function updateSertifikat(Request $request)
    {
        $request->validate([
            'template' => 'required|string'
        ]);

        KonfigurasiSistem::updateOrCreate(
            ['key' => 'template_sertifikat'],
            ['value' => $request->template]
        );

        return back()->with('success', 'Template sertifikat berhasil diperbarui.');
    }
}
