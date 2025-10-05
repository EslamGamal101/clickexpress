<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\DriverResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileUserController extends Controller
{
    
    public function show(Request $request)
    {
        $user = $request->user();

        $data = [
            'id'        => $user->id,
            'phone'     => $user->phone,
            'type'      => $user->type,
            'is_active' => $user->is_active,
            'is_verified_id' => $user->is_verified_id,
            'profile'   => new ProfileResource($user->profile),
            'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
        ];

        return ApiResponse::SendRespond(200, 'تم جلب البيانات بنجاح', $data);
    }



    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $profile = $user->profile;
            if (!$profile) {
                return ApiResponse::SendRespond(404, 'البروفايل غير موجود', []);
            }

            // 🔹 الخطوة 1: البيانات المشتركة
            $commonData = $request->only(['first_name', 'last_name', 'city', 'area', 'email', 'national_id']);
            $profile->fill($commonData)->save();

            // 🔹 الخطوة 2: تحديث رقم التليفون
            if ($request->filled('phone')) {
                $user->update(['phone' => $request->phone]);
            }

            // 🔹 الخطوة 3: الصور العامة
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profiles', 'public');
                $profile->profile_image = $path;
            }

            // ✅ صورة الهوية (ID Image)
            if ($request->hasFile('id_image')) {
                $path = $request->file('id_image')->store('id_cards', 'public');
                $profile->id_image = $path;
            }

            // 🔹 الخطوة 4: تحديث حسب نوع المستخدم
            switch ($user->type) {
                case 'vendor':
                    $profile->vendor_name = $request->vendor_name ?? $profile->vendor_name;
                    break;

                case 'driver':
                    if ($request->hasFile('car_image')) {
                        $profile->car_image = $request->file('car_image')->store('cars', 'public');
                    }
                    if ($request->hasFile('license_image')) {
                        $profile->license_image = $request->file('license_image')->store('licenses', 'public');
                    }
                    $driverData = $request->only(['vehicle_type', 'vehicle_plate', 'work_place']);
                    $profile->fill($driverData);
                    break;
            }

            // 🔹 الخطوة 5: حفظ التغييرات
            $profile->save();

            DB::commit();

            $user->load('profile');

            $resource = ($user->type === 'driver') ? new DriverResource($user) : new UserResource($user);

            return ApiResponse::SendRespond(200, 'تم تحديث البروفايل بنجاح', $resource);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Profile update failed", [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return ApiResponse::SendRespond(500, 'حدث خطأ أثناء تحديث البروفايل', ['error' => $e->getMessage()]);
        }
    }
}
