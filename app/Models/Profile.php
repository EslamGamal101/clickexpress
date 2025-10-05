<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'national_id',
        'city',
        'area',
        'vendor_name',
        'vehicle_type',
        'vehicle_plate',
        'license_image',
        'car_image',
        'profile_image',
        'id_image',
    ];

    // 🔹 العلاقة مع User (عكس hasOne)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
