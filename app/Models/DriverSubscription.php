<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'package_id',
        'activated_at',
        'expires_at',
        'remaining_rides',
        'status',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // العلاقات
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
