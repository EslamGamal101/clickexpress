<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * عرض قائمة المسؤولين مع التحقق من صلاحية العرض
     */
    public function index()
    {
        $admins = User::whereIn('type', ['admin']) 
            ->orWhereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'super_admin']);
            })
            ->get();

        return view('admins.index', compact('admins'));
    }
    public function create()
    {

        $roles = Role::whereIn('name', ['admin', 'super_admin'])->get();
        return view('admins.create', compact('roles'));
    }

    /**
     * تخزين مسؤول جديد في قاعدة البيانات
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
        ]);

        $admin = User::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'type' => 'admin', // تعيين النوع كـ admin افتراضياً
            'is_active' => true, // تفعيل الحساب
        ]);

        // تعيين الدور للمستخدم
        $admin->assignRole($request->role);

        return redirect()->route('admins.index')->with('success', 'تم إضافة المسؤول بنجاح.');
    }

    /**
     * حذف مسؤول (يجب أن يكون المسؤول الرئيسي فقط)
     */
    public function destroy(User $admin)
    {
        $this->authorize('delete_admins');
        if (auth()->id() === $admin->id || $admin->hasRole('super_admin')) {
            return redirect()->route('admins.index')->with('error', 'لا يمكن حذف هذا المسؤول.');
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'تم حذف المسؤول بنجاح.');
    }
}
