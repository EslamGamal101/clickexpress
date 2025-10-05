<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'price',
        'started_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
