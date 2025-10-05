<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Order;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function rateDriver(Request $request, $driver_id, $order_id)
    {
        $driver = User::find($driver_id);
        if (!$driver) {
            return ApiResponse::SendRespond(404, 'السائق غير موجود', []);
        }
        $order = Order::find($order_id);
        if (!$order) {
            return ApiResponse::SendRespond(404, 'الطلب غير موجود', []);
        }

        // شرط: التقييم فقط بعد التوصيل
        if ($order->status !== 'delivered') {
            return ApiResponse::SendRespond(403, 'لا يمكنك تقييم السائق قبل التوصيل', []);
        }
        $request->validate([
            'rate_driver' => 'required|integer|min:1|max:5',
            'rate_app' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ], [
            'rate_driver.required' => 'يرجى تقييم السائق',
            'rate_driver.integer' => 'تقييم السائق يجب أن يكون رقم',
            'rate_driver.min' => 'التقييم لا يمكن أن يكون أقل من 1',
            'rate_driver.max' => 'التقييم لا يمكن أن يكون أكبر من 5',
            'rate_app.integer' => 'تقييم التطبيق يجب أن يكون رقم',
            'rate_app.min' => 'التقييم لا يمكن أن يكون أقل من 1',
            'rate_app.max' => 'التقييم لا يمكن أن يكون أكبر من 5',
            'comment.string' => 'الملاحظات يجب أن تكون نصاً',
            'comment.max' => 'الملاحظات لا يمكن أن تتجاوز 500 حرف'
        ]);

        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'driver_id' => $driver->id,
            'rate_driver' => $request->rate_driver,
            'rate_app' => $request->rate_app,
            'comment' => $request->comment
        ]);

        return ApiResponse::SendRespond(200, 'تم إرسال التقييم والملاحظات بنجاح', $rating);
    }
}
