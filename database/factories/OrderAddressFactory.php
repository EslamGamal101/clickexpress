<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressFactory extends Factory
{
    protected $model = \App\Models\OrderAddress::class;

    public function definition(): array
    {
        return [
            'pickup_city'    => $this->faker->city(),
            'pickup_area'    => $this->faker->word(),
            'pickup_phone'   => '079' . $this->faker->numberBetween(1000000, 9999999),
            'pickup_name'    => $this->faker->name(),
            'delivery_city'  => $this->faker->city(),
            'delivery_area'  => $this->faker->word(),
            'delivery_phone' => '078' . $this->faker->numberBetween(1000000, 9999999),
            'delivery_name'  => $this->faker->name(),
        ];
    }
}
