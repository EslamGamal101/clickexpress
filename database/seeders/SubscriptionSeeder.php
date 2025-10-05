<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\SubscriptionCode;
use App\Models\DriverSubscription;
use App\Models\DriverMembership;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ إنشاء باقات رحلات
        $ridePackages = Package::factory()->count(3)->create([
            'duration_days' => null, // باقات الرحلات
        ]);

        // 2️⃣ إنشاء باقات شهرية
        $monthlyPackages = Package::factory()->count(2)->create([
            'duration_days' => 30,
        ]);

        // 3️⃣ إنشاء أكواد مرتبطة بالباقات
        $ridePackages->each(function ($package) {
            SubscriptionCode::factory()->count(5)->create([
                'package_id' => $package->id,
            ]);
        });

        $monthlyPackages->each(function ($package) {
            SubscriptionCode::factory()->count(5)->create([
                'package_id' => $package->id,
            ]);
        });

        // 4️⃣ إنشاء اشتراكات رحلات للسائقين
        DriverSubscription::factory()->count(5)->create([
            'package_id' => $ridePackages->random()->id,
        ]);

        // 5️⃣ إنشاء اشتراكات شهرية للسائقين
        DriverMembership::factory()->count(5)->create();
    }
}
