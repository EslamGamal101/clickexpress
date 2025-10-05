<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppInfo extends Model
{
    use HasFactory;
    protected $table = 'app_info';

    protected $fillable = [
        'terms',
        'whatsapp',
        'facebook',
        'instagram',
        'about_us'
    ];
}
