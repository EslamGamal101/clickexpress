<?php

namespace App\Helpers;

class GeoHelper
{
    /**
     * حساب المسافة بالكيلومترات بين نقطتين (lat1, lng1) و (lat2, lng2)
     */
    public static function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
