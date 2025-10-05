<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * تشغيل عمليات التغذية (Seeding) لقاعدة البيانات.
     */
    public function run(): void
    {
        // لضمان عدم تكرار الأخطاء، نستخدم guard web الافتراضي
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. تعريف الصلاحيات الأساسية
        $permissions = [
            'view_admins',     // عرض جدول المسؤولين
            'create_admins',   // إضافة مسؤول جديد
            'edit_admins',     // تعديل بيانات المسؤولين (مهم إضافتها)
            'delete_admins',   // حذف مسؤول
            'view_statistics', // الإحصائيات والتقارير (للمسؤول الرئيسي فقط)

            // يمكنك إضافة صلاحيات أخرى هنا مثل:
            // 'manage_vendors', 
            // 'manage_drivers', 
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // جلب جميع الصلاحيات بعد إنشائها
        $allPermissions = Permission::all();


        // الدور 1: المسؤول الرئيسي (Super Admin)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo($allPermissions); // يمتلك جميع الصلاحيات

        // الدور 2: المسؤول العادي (Admin)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // نعطي المسؤول العادي الصلاحيات المحددة (مثال: يمكنه فقط العرض والإضافة)
        $adminRole->givePermissionTo([
            'view_admins',
            'create_admins',
            'edit_admins',
            // لا يملك 'delete_admins' ولا 'view_statistics'
        ]);

        // 3. ربط المستخدم الحالي بدور المسؤول الرئيسي (اختياري لكن مفيد)
        // هذا يفترض أن لديك مستخدم واحد على الأقل في جدول users
        /*
        $user = \App\Models\User::where('email', 'your_super_admin_email@example.com')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }
        */

        // ملاحظة: يمكنك هنا ربط نوع المستخدم 'management_producers' بدور معين إذا لزم الأمر
    }
}
