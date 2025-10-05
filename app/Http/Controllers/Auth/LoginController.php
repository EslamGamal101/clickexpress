<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::SendRespond(401, 'بيانات تسجيل الدخول غير صحيحة', []);
        }

        // 🔹 توليد OTP وتخزينه في جدول verification_codes
        $otp = rand(100000, 999999);
        VerificationCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Log::info("OTP for {$user->phone}: {$otp}");

        return ApiResponse::SendRespond(200, 'تم إرسال كود التحقق OTP إلى هاتفك', []);
    }

    

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ApiResponse::SendRespond(200, 'تم تسجيل الخروج بنجاح', []);
    }
}
