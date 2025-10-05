<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * عرض نموذج تسجيل الدخول (Admin Login Page).
     */
    public function showLoginForm()
    {
        // عرض الـ View الذي قمت بتصميمه
        return view('auth.admin_login');
    }

    /**
     * معالجة طلب تسجيل الدخول.
     */
    public function login(Request $request)
    {
        dd($request->all());
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
      
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {

            $user = Auth::user();
            if ($user->type === 'admin' ) {
                $request->session()->regenerate();
                return to_route('admin.index');
            }


            // إذا كان المستخدم موجوداً ولكنه ليس مسؤولاً (مثل customer/driver)، نقوم بتسجيل خروجه
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'هذه البيانات لا تخص مسؤول في النظام.',
            ]);
        }

        // 4. فشل عملية المصادقة (كلمة مرور أو بريد إلكتروني خاطئ)
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * تسجيل الخروج من النظام.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // التوجيه إلى صفحة تسجيل دخول المسؤول بعد الخروج
        return redirect()->route('admin.login.form');
    }
}
