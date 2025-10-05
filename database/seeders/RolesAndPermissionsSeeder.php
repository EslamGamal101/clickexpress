<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ✅ حذف آمن بدون كسر العلاقات
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::query()->delete();
        Role::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // إنشاء الصلاحيات
        $permissions = [
            'view_reports',
            'manage_users',
            'create_orders',
            'delete_orders',
            'edit_orders',
            'view_orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء الأدوار
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $orderAdmin = Role::firstOrCreate(['name' => 'order_admin']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // ربط الصلاحيات بالأدوار
        $superAdmin->givePermissionTo(Permission::all());
        $orderAdmin->givePermissionTo(['create_orders', 'edit_orders', 'view_orders', 'delete_orders']);
        $viewer->givePermissionTo(['view_orders']);

        // ربط أول مستخدم بدور الأدمن
        $user = \App\Models\User::find(1);
        if ($user) {
            $user->assignRole('super_admin');
        }

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح!');
    }
}
