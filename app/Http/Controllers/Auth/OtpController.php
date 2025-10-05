<?php

namespace App\Http\Controllers\Auth;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|exists:users,phone',
                'otp'   => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::SendRespond(422, 'رقم الهاتف غير صحيح أو غير مسجل', []);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return ApiResponse::SendRespond(401, 'المستخدم غير موجود', []);
        }

        $verification = VerificationCode::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$verification) {
            return ApiResponse::SendRespond(404, 'لم يتم إرسال أي كود تحقق', []);
        }
        
        if ($verification->attempts >= 5) {
            if ($verification->updated_at->gt(now()->subMinutes(1))) {
                return ApiResponse::SendRespond(429, 'محاولات كثيرة جدًا، برجاء المحاولة بعد دقيقه', []);
            } else {
                $verification->update(['attempts' => 0]);
            }
        }

        // 🔹 تحقق من الكود
        if (!Hash::check($request->otp, $verification->code)) {
            $verification->increment('attempts');
            return ApiResponse::SendRespond(401, 'الكود غير صحيح', []);
        }

        // 🔹 تحقق من الصلاحية
        if (Carbon::now()->gt($verification->expires_at)) {
            return ApiResponse::SendRespond(403, 'الكود انتهت صلاحيته', []);
        }

        // 🔹 نجاح → امسح الكود وأصدر توكن
        $verification->delete();

        $token = $user->createToken('mobile_app')->plainTextToken;
        $user->load('profile');

        $data = [
            'user'  => new UserResource($user),
            'type'  => $user->type,
            'token' => $token,
        ];

        return ApiResponse::SendRespond(200, 'تم تسجيل الدخول بنجاح', $data);
    }


    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|exists:users,phone',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::SendRespond(422, 'رقم الهاتف غير صحيح أو غير مسجل', []);
        }

        $user = User::where('phone', $request->phone)->first();
        if( !$user ) {
            return ApiResponse::SendRespond(401, 'المستخدم غير موجود', []);
        }
        $otp = rand(100000, 999999);

        VerificationCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts'   => 0,
        ]);

        Log::info("OTP for {$user->phone}: {$otp}");

        return ApiResponse::SendRespond(200, 'تم إرسال كود التحقق OTP إلى هاتفك', [
            'user' => [
                'phone' => $user->phone,
                'type'  => $user->type,
            ]
        ]);
    }
}
