<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * 
     */
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function store(RegisterRequest $request, OtpService $otpService)
    {
        $validated = $request->validated();

        // 🔹 إنشاء المستخدم
        $user = User::create([
            'phone'     => $validated['phone'],
            'password'  => Hash::make($validated['password']),
            'type'      => $validated['type'],
            'is_active' => $validated['type'] === 'driver' ? false : true,
        ]);
        $profileData = match ($user->type) {
            'customer' => [
                'first_name' => $request->first_name ?? null,
                'last_name'  => $request->last_name ?? null,
                'city'       => $request->city ?? null,
                'area'       => $request->area ?? null,
                'national_id' => $request->national_id ?? null,
            ],
            'vendor' => [
                'first_name' => $request->first_name ?? null,
                'last_name'  => $request->last_name ?? null,
                'city'       => $request->city ?? null,
                'area'       => $request->area ?? null,
                'vendor_name' => $request->vendor_name ?? null,
                'city'        => $request->city ?? null,
                'area'        => $request->area ?? null,
                'national_id' => $request->national_id ?? null,
            ],
            'management_producers' => [
                'first_name' => $request->first_name ?? null,
                'last_name'  => $request->last_name ?? null,
                'city'       => $request->city ?? null,
                'area'       => $request->area ?? null,
                'city'        => $request->city ?? null,
                'area'        => $request->area ?? null,
                'national_id' => $request->national_id ?? null,
            ],
            'driver' => [
                'first_name'    => $request->first_name ?? null,
                'last_name'     => $request->last_name ?? null,
                'vehicle_type'  => $request->vehicle_type ?? null,
                'vehicle_plate' => $request->vehicle_plate ?? null,
                'license_image' => $request->license_image ?? null,
                'car_image'     => $request->car_image ?? null,
                'profile_image' => $request->profile_image ?? null,
                'national_id' => $request->national_id ?? null,
            ],
            'admin' => [
                'first_name' => $request->first_name ?? null,
                'last_name'  => $request->last_name ?? null,
            ],
            default => [],
        };
       $user->profile()->create($profileData);
       $otpService->createAndSend($user);

       // Log::info("OTP for {$user->phone}: {$otp}");

        return ApiResponse::SendRespond(200, 'تم إرسال كود التحقق OTP إلى هاتفك', [
            'user' => [
                'phone' => $user->phone,
                'type'  => $user->type,
            ]
        ]);
    }
   
}
