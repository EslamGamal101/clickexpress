<?php

namespace App\Http\Controllers;
use App\Helpers\ApiResponse;
use App\Models\DeviceToken;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * عرض كل الإشعارات للـ user الحالي (بغض النظر عن نوعه: user/driver/vendor)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = Notification::where('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::SendRespond(200, 'قائمة الإشعارات', $notifications);
    }

    /**
     * وضع إشعار كمقروء
     */
    public function markAsRead($id, Request $request): JsonResponse
    {
        // فالديشن للـ ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::SendRespond(422, 'خطأ في البيانات', $validator->errors());
        }

        $user = $request->user();

        // التأكد إن الإشعار ينتمي للمستخدم
        $notification = Notification::where('id', $id)
            ->where('receiver_id', $user->id)
            ->first();

        if (!$notification) {
            return ApiResponse::SendRespond(404, 'الإشعار غير موجود أو لا يخص هذا المستخدم', []);
        }

        // تعليم الإشعار كمقروء
        $notification->update(['is_read' => true]);

        return ApiResponse::SendRespond(200, 'تم تعليم الإشعار كمقروء', $notification);
    }

    public function destroy($id, Request $request): JsonResponse
    {
        // فالديشن للـ ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::SendRespond(422, 'خطأ في البيانات', $validator->errors());
        }

        $user = $request->user();

        // التأكد إن الإشعار ينتمي للمستخدم
        $notification = Notification::where('id', $id)
            ->where('receiver_id', $user->id)
            ->first();

        if (!$notification) {
            return ApiResponse::SendRespond(404, 'الإشعار غير موجود أو لا يخص هذا المستخدم', []);
        }

        // حذف الإشعار
        $notification->delete();

        return ApiResponse::SendRespond(200, 'تم حذف الإشعار', []);
    }

    /**
     * إرسال إشعار (للتجربة فقط)
     */
    public function send(Request $request): JsonResponse
    {
        dd($request->all());
        Log::info('Notification Request:', $request->all());
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:user,driver,vendor',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // حفظ في جدول الإشعارات
        $notification = Notification::create([
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'title' => $request->title,
            'body' => $request->body,
            'is_read' => false,
        ]);

        // جلب كل التوكنات للجهاز/المستخدم الهدف
        $tokens = DeviceToken::where('owner_id', $request->receiver_id)
            ->where('owner_type', $request->receiver_type)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        // 👇 هنا تقدر تستدعي Helper تبعك لإرسال Firebase Notification
        // FcmHelper::sendNotification($tokens, $request->title, $request->body);

        return ApiResponse::SendRespond(200, 'تم إرسال الإشعار', [
            'notification' => $notification,
            'tokens' => $tokens
        ]);
    }
    public function create()
    {
        return view('notifications.create');
    }
}
