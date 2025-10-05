<?php
namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $length = 5;
    protected $ttlMinutes = 5;

    // توليد كود
    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 99999), $this->length, '0', STR_PAD_LEFT);
    }

    // إرسال SMS باستخدام API خارجي
    protected function sendSms(string $phone, string $message)
    {
        // مثال باستخدام Nexmo (تبدله ببيانات مزودك)
        $apiKey = config('services.nexmo.key');
        $apiSecret = config('services.nexmo.secret');
        $from = config('services.nexmo.sms_from');

        Http::post("https://rest.nexmo.com/sms/json", [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'to' => $phone,
            'from' => $from,
            'text' => $message,
        ]);

    }

    // إنشاء وإرسال OTP
    public function createAndSend(User $user)
    {
        $code = $this->generateCode();

        // خزّن في DB
        VerificationCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes($this->ttlMinutes),
            'attempts'   => 0,
        ]);

        Log::info("OTP for {$user->phone}: {$code}");

        // ابعت SMS هنا
        // $this->sendSms($user->phone, "رمز التحقق الخاص بك هو: {$code}");
    }


    // // التحقق
    // public function verify(string $phone, string $code): bool
    // {
    //     $hash = Cache::get("otp:{$phone}");
    //     if (! $hash) {
    //         return false; // OTP expired or not exist
    //     }

    //     return Hash::check($code, $hash);
    // }
}
