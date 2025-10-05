<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = ['owner_id', 'owner_type', 'token', 'platform', 'is_active', 'last_seen_at'];

    public function owner()
    {
        return $this->morphTo();
    }
}
