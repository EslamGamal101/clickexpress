<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'pickup_city',
        'pickup_area',
        'pickup_name',
        'pickup_phone',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_city',
        'delivery_area',
        'delivery_name',
        'delivery_phone',
        'delivery_latitude',
        'delivery_longitude',
    ];

    /**
     * Get the order that owns the address.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
