<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['receiver_id', 'type', 'title', 'body', 'data', 'is_read','sender_id'];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
