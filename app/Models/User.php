<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'type',
        'is_active',
        'is_verified_id',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function verificationCodes()
    {
        return $this->hasMany(VerificationCode::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'driver_id');
    }
    // public function userSubscription()
    // {
    //     return $this->hasOne(UserSubscription::class);
    // }

    // التقييمات اللي عملها المستخدم
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }
    public function driverLocation()
    {
        return $this->hasOne(DriverLocation::class, 'driver_id');
    }
    public function subscription()
    {
        return $this->hasMany(DriverSubscription::class, 'driver_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }
    public function activeMembership()
    {
        return $this->hasOne(DriverMembership::class, 'driver_id')
            ->where('is_active', true)
            ->where('expires_at', '>', Carbon::now());
    }

    public function activeSubscription()
    {
        return $this->hasOne(DriverSubscription::class, 'driver_id')
            ->where('status', 'active');
    }
}
