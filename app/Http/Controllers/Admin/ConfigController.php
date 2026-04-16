<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = KonfigurasiSistem::all();
        return view('admin.config.index', compact('configs'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            KonfigurasiSistem::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
