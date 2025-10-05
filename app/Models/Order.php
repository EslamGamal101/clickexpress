<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_type',
        'delivery_type',
        'delivery_date',
        'package_type',
        'package_other',
        'price',
        'delivery_fee',
        'notes',
        'assign_last_driver',
        'status',
        'vehicle_type',
        'cancellation_reason',
        'driver_id',
        'tracking_code',
        'serial_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
    
}
