<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'order_id',
        'rate_driver',
        'rate_app',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // اللي عمل التقييم
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id'); // السائق
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
