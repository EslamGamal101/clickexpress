<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    // Scope للبحث القريبين
    public function scopeNearby($query, $lat, $lng, $radiusKm = 5)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query->selectRaw("drivers.*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }
}
