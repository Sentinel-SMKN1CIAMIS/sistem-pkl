<?php

namespace App\Services;

use App\Models\KonfigurasiSistem;

class PdfKopService
{
    public static function getKopData(): array
    {
        $defaults = [
            'report_kop_baris_1' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT',
            'report_kop_baris_2' => 'DINAS PENDIDIKAN',
            'report_kop_baris_3' => 'CABANG DINAS PENDIDIKAN WILAYAH XIII',
            'report_kop_baris_4' => 'SMK NEGERI 1 CIAMIS',
            'report_kop_baris_5' => 'Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204',
            'report_kop_baris_6' => 'Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net',
            'report_kop_baris_7' => 'Ciamis – 46215',
        ];

        $configs = collect();
        try {
            $configs = KonfigurasiSistem::whereIn('key', array_keys($defaults))
                ->pluck('value', 'key');
        } catch (\Throwable $e) {
            // Fallback to defaults if table is unavailable
        }

        $data = [];
        foreach ($defaults as $key => $fallback) {
            $val = $configs->get($key);
            $data[$key] = ($val !== null && trim((string) $val) !== '') ? $val : $fallback;
        }

        $jabarPath = public_path('jabar.png');
        $smeaPath = public_path('smea.png');
        $data['logo_left_path'] = file_exists($jabarPath) ? $jabarPath : null;
        $data['logo_right_path'] = file_exists($smeaPath) ? $smeaPath : null;

        return $data;
    }
}
