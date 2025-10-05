<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class DeviceTokenController extends Controller
{
    // حفظ أو تحديث التوكن
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::SendRespond(401, 'غير مسموح، يجب تسجيل الدخول', []);
        }
        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string|in:android,ios,web',
        ]);

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'token' => $request->token,
            ],
            [
                'owner_id' => $user->id,
                'owner_type' => $user->type,
                'platform' => $request->platform,
                'is_active' => true,
                'last_seen_at' => now(),
            ]
        );

        return ApiResponse::SendRespond(200, 'تم حفظ التوكن بنجاح', $deviceToken);
    }

    // حذف التوكن
    public function destroy($token, Request $request)
    {
        $user = $request->user();

        $deleted = DeviceToken::where('token', $token)
            ->where('owner_id', $user->id)
            ->where('owner_type', $user->type)
            ->delete();

        if (!$deleted) {
            return ApiResponse::SendRespond(404, 'التوكن غير موجود أو لا يخص هذا المستخدم', []);
        }

        return ApiResponse::SendRespond(201, 'تم حذف التوكن بنجاح', []);
    }
    public function update(Request $request, $oldToken)
    {
        $user = $request->user();

        $request->validate([
            'token'     => 'required|string', // التوكن الجديد
            'platform'  => 'nullable|string|in:android,ios,web',
            'is_active' => 'nullable|boolean',
        ]);

        $deviceToken = DeviceToken::where('token', $oldToken)
            ->where('owner_id', $user->id)
            ->where('owner_type', $user->type)
            ->first();

        if (!$deviceToken) {
            return ApiResponse::SendRespond(404, 'التوكن غير موجود أو لا يخص هذا المستخدم', []);
        }

        // تحديث التوكن الجديد
        $deviceToken->token = $request->token;

        if ($request->has('platform')) {
            $deviceToken->platform = $request->platform;
        }

        if ($request->has('is_active')) {
            $deviceToken->is_active = $request->is_active;
        }

        $deviceToken->last_seen_at = now();
        $deviceToken->save();

        return ApiResponse::SendRespond(200, 'تم تحديث التوكن بنجاح', $deviceToken);
    }
}
