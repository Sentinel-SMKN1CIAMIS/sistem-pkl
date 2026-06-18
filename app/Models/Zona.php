<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $fillable = ['nama', 'warna', 'warna_border', 'koordinat_geojson', 'nomor_zona'];

    protected $casts = [
        'koordinat_geojson' => 'array',
    ];

    public function dudis()
    {
        return $this->hasMany(Dudi::class);
    }

    /**
     * Check if a point (lat/lng) is inside this zone's polygon.
     * Uses Ray-Casting algorithm.
     */
    public function containsPoint(float $lat, float $lng): bool
    {
        $polygon = $this->koordinat_geojson;
        if (!is_array($polygon) || count($polygon) < 3) {
            return false;
        }

        $n = count($polygon);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            if (!is_array($polygon[$i]) || !is_array($polygon[$j]) || !isset($polygon[$i][0], $polygon[$i][1], $polygon[$j][0], $polygon[$j][1])) {
                return false;
            }

            $xi = $polygon[$i][1]; // lat
            $yi = $polygon[$i][0]; // lng
            $xj = $polygon[$j][1]; // lat
            $yj = $polygon[$j][0]; // lng

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Detect which zona a given coordinate falls into.
     * Returns Zona model or null.
     */
    public static function detectZona(float $lat, float $lng): ?self
    {
        $zonas = self::all();
        foreach ($zonas as $zona) {
            if ($zona->containsPoint($lat, $lng)) {
                return $zona;
            }
        }
        return null;
    }
}
