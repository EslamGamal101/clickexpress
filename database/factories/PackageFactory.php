<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition()
    {
        return [
            'name'          => $this->faker->word() . " Package",
            'price'         => $this->faker->randomFloat(2, 1, 10), // من 1 لـ 10 دينار
            'rides_count'   => $this->faker->numberBetween(5, 50),
            'duration_days' => $this->faker->randomElement([null, 30]), // يا إما شهر أو null
            'is_active'     => true,
        ];
    }
}
