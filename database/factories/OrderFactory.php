<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'user_id'       => \App\Models\User::factory(), // أو مستخدم موجود
            'driver_id'     => null,
            'order_type'    => $this->faker->randomElement(['package', 'cargo']),
            'delivery_type' => $this->faker->randomElement(['instant', 'same_day']),
            'package_type'  => $this->faker->randomElement(['small_box', 'medium_box', 'large_box', 'other']),
            'package_other' => $this->faker->optional()->word(),
            'price'         => $this->faker->randomFloat(2, 5, 100),
            'delivery_fee'  => $this->faker->randomFloat(2, 2, 20),
            'notes'         => $this->faker->sentence(),
            'vehicle_type'  => $this->faker->randomElement(['motorcycle', 'car', 'van']),
            'status'        => 'pending',
            'tracking_code' => Str::upper(Str::random(10)),
            'serial_number' => Str::upper(Str::random(12)),
        ];
    }
}
