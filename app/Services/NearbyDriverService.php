<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\DriverLocation;
use Illuminate\Support\Collection;

class NearbyDriverService
{
    /**
     * Get nearby drivers with device tokens and 'distance' attribute.
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusKm
     * @param int|null $limit
     * @param int|null $onlyRecentMinutes filter drivers whose last_location_at >= now() - minutes
     * @return Collection
     */
    public function getNearbyDrivers($lat, $lng, $radiusKm = 5, $limit = null, $onlyRecentMinutes = 15)
    {
        // Validation basic
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return collect();
        }

        // --- 1) Bounding box (تصفية سريعة) ---
        // degree per km ~ 1/111.045 (تقريب)
        $latDelta = $radiusKm / 111.045;
        // تصحيح الطول حسب خط العرض
        $lngDelta = $radiusKm / (111.045 * cos(deg2rad($lat)));

        $minLat = $lat - $latDelta;
        $maxLat = $lat + $latDelta;
        $minLng = $lng - $lngDelta;
        $maxLng = $lng + $lngDelta;

        $query = DriverLocation::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng]);

        if ($onlyRecentMinutes) {
            $query->where('last_location_at', '>=', now()->subMinutes($onlyRecentMinutes));
        }

        // --- 2) Haversine for precise distance ---
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        $query->selectRaw("drivers.*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');

        if ($limit) {
            $query->limit($limit);
        }

        // نجيب مع السائق توكنات الأجهزة
        $drivers = $query->with('deviceTokens')->get();

        return $drivers;
    }
}
