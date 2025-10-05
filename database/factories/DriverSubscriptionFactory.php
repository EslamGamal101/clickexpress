<?php

namespace Database\Factories;

use App\Models\DriverSubscription;
use App\Models\User;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DriverSubscriptionFactory extends Factory
{
    protected $model = DriverSubscription::class;

    public function definition()
    {
        $activatedAt = Carbon::now()->subDays(rand(1, 10));
        $expiresAt   = (clone $activatedAt)->addDays(30);

        return [
            'driver_id'      => User::factory(),
            'package_id'     => Package::factory(),
            'activated_at'   => $activatedAt,
            'expires_at'     => $expiresAt,
            'remaining_rides' => $this->faker->numberBetween(0, 50),
            'status'         => $this->faker->randomElement(['active', 'expired', 'cancelled']),
        ];
    }
}
