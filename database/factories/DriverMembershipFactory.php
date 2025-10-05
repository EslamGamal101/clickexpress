<?php

namespace Database\Factories;

use App\Models\DriverMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DriverMembershipFactory extends Factory
{
    protected $model = DriverMembership::class;

    public function definition()
    {
        $startedAt = Carbon::now()->subDays(rand(1, 10));
        $expiresAt = (clone $startedAt)->addMonth();

        return [
            'driver_id'  => User::factory(),
            'price'      => 1.0, // سعر الاشتراك الشهري ثابت مثلاً
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
            'is_active'  => true,
        ];
    }
}
