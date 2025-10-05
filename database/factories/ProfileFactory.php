<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'national_id'   => $this->faker->unique()->numerify('###########'),
            'city'          => $this->faker->city(),
            'area'          => $this->faker->streetName(),
            'vendor_name'   => null,
            'vehicle_type'  => null,
            'vehicle_plate' => null,
            'license_image' => null,
            'car_image'     => null,
            'profile_image' => $this->faker->imageUrl(200, 200),
        ];
    }

    // --------------------------------------
    // هنا تحط ال states لكل نوع مستخدم
    // --------------------------------------

    public function driver(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'vehicle_type'  => $this->faker->randomElement(['car', 'bike', 'truck']),
                'vehicle_plate' => strtoupper($this->faker->bothify('??###??')),
                'license_image' => $this->faker->imageUrl(200, 200),
                'car_image'     => $this->faker->imageUrl(200, 200),
            ];
        });
    }

    public function vendor(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'vendor_name'   => $this->faker->company(),
            ];
        });
    }

    public function customer(): static
    {
        return $this->state(function (array $attributes) {
            return [];
        });
    }
}
