<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'package_id',
        'is_used',
        'used_by',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // علاقة مع الباقة
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function usedByDriver()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
