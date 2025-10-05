<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدمن فقط إذا لم يكن موجودًا بالفعل
        $admin = User::firstOrCreate(
            ['email' => 'admin@clickexpress.com'],
            [
                'phone' => '01000000000',
                'password' => Hash::make('123456'),
                'type' => 'admin',
                'is_active' => true,
                'is_verified_id' => true,
            ]
        );

        // إنشاء بروفايل الأدمن (لو مش موجود)
        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'first_name' => 'مدير',
                'last_name' => 'النظام',
                'city' => 'القاهرة',
                'area' => 'مصر الجديدة',
                'profile_image' => null,
            ]
        );
    }
}
