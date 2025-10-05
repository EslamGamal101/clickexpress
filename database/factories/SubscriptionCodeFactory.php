<?php

namespace Database\Factories;

use App\Models\SubscriptionCode;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionCodeFactory extends Factory
{
    protected $model = SubscriptionCode::class;

    public function definition()
    {
        return [
            'code'       => strtoupper($this->faker->bothify('???###')), // مثال: ABC123
            'package_id' => Package::factory(),
            'is_used'    => false,
            'used_by'    => null,
            'used_at'    => null,
            'expires_at' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
        ];
    }
}
