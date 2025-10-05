<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class ReportController extends Controller
{
    // GET /reports/summary
    public function summary(Request $request)
    {
        $userId = $request->user()->id; // نفترض المستخدم مسجل دخول

        // الإحصائيات
        $completedOrders = Order::where('user_id', $userId)
            ->where('status', 'delivered')
            ->count();

        $canceledOrders = Order::where('user_id', $userId)
            ->where('status', 'cancelled')
            ->count();

        $pendingOrders = Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        // آخر أوردر مكتمل
        $lastOrder = Order::where('user_id', $userId)
            ->where('status', 'delivered')
            ->first();

        // التقييمات
        $reviewsCount  = Rating::where('user_id', $userId)->count();
        $driversRating = Rating::where('user_id', $userId)->avg('rate_driver') ?? 0;

        // بناء البيانات
        $data = [
            'completed_orders' => $completedOrders,
            'canceled_orders'  => $canceledOrders,
            'pending_orders'   => $pendingOrders,
            'reviews_count'  => $reviewsCount,
            'drivers_rating' => round($driversRating, 2),
            'tip'            => '📌 راجع سجل الطلبات لتفاصيل كل عملية وقيم الكباتن لتحسين تجربتك'
        ];

        return ApiResponse::SendRespond(200, 'ملخص النشاط', $data);
    }

    public function driver_summary(Request $request)
    {
        $user = $request->user();

        // متوسط التقييم
        $averageRating = Rating::where('driver_id', $user->id)->avg('rate_driver');

        // إحصائيات عامة
        $driverReport = [
            'total_rides'    => $user->orders()->count(),
            'average_rating' => round($averageRating, 2) ?? 0,
            'total_earnings' => $user->orders()->sum('delivery_fee'),
        ];

        // أرباح اليوم / الأسبوع / الشهر
        $earningsSummary = [
            'today'      => $user->orders()->whereDate('created_at', today())->sum('delivery_fee'),
            'this_week'  => $user->orders()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('delivery_fee'),
            'this_month' => $user->orders()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('delivery_fee'),
        ];

        // الرحلات المكتملة اليوم / الأسبوع / الشهر
        $completedRides = [
            'today'      => $user->orders()->where('status', 'completed')->whereDate('created_at', today())->count(),
            'this_week'  => $user->orders()->where('status', 'completed')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $user->orders()->where('status', 'completed')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return ApiResponse::SendRespond(200, 'التقارير', [
            'driver'          => $driverReport,
            'earnings'        => $earningsSummary,
            'completed_rides' => $completedRides,
        ]);
    }
}
