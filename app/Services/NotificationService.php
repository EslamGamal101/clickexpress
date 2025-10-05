<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * إنشاء إشعار + إرساله إلى Firebase
     */
    public static function send(
        int $receiverId,
        string $type,
        string $title,
        string $body,
        array $data = [],
        ?int $senderId = null
    ) {
        // 1) خزّن الإشعار في قاعدة البيانات
        $notification = Notification::create([
            'receiver_id' => $receiverId,
            'sender_id'   => $senderId,
            'type'        => $type,
            'title'       => $title,
            'body'        => $body,
            'data'        => $data,
        ]);

        // 2) هات التوكنات المرتبطة بالمستلم
        $tokens = DeviceToken::where('user_id', $receiverId)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        // 3) ابعت Firebase لو في توكنات
        if (count($tokens) > 0) {
            self::sendFirebase($tokens, $title, $body, $data, $type);
        }

        return $notification;
    }

    /**
     * إرسال إشعار إلى Firebase Cloud Messaging
     */
    private static function sendFirebase(array $tokens, string $title, string $body, array $data, string $type)
    {
        $serverKey = config('services.fcm.server_key'); // هتحطه في .env

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body'  => $body,
                'sound' => $type, // هنا ممكن تحدد نغمة حسب نوع الإشعار
            ],
            'data' => $data,
        ];

        Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', $payload);
    }
}
