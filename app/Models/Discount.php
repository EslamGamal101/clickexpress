<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'target_type',
        'driver_id',
        'package_id',
        'title',
        'occasion',
        'discount_type',
        'value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // 🧩 العلاقات
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
