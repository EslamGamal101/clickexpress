<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderAddress;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        
        Order::factory(10)->create()->each(function ($order) {
            $address = \App\Models\OrderAddress::factory()->make();
            $order->address()->save($address);
        });
    }
}
