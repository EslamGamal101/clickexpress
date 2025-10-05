<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationFactory extends Factory
{
    
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'sender_id'   => User::inRandomOrder()->first()?->id, // اختيار عشوائي للمرسل
            'receiver_id' => User::inRandomOrder()->first()?->id, // اختيار عشوائي للمستقبل
            'type'        => $this->faker->randomElement([
                'new_order',
                'order_accepted',
                'order_completed',
                'discount',
                'reminder',
                'system_update',
                'driver_dispute',
            ]),
            'title'       => $this->faker->sentence(3),
            'body'        => $this->faker->paragraph(1),
            'data'        => json_encode([
                'order_id' => $this->faker->randomNumber(5),
                'extra'    => $this->faker->word,
            ]),
            'is_read'     => $this->faker->boolean(30), // 30% احتمال يكون مقروء
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
