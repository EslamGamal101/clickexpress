<?php

use App\Http\Controllers\DeliveryPricingController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\DriverLocationController;
use App\Http\Controllers\DriverOrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;











/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function () {

    // API عرض الـ profile
    Route::get('/profile', [ProfileUserController::class, 'show']);
    // API تعديل الـ profile
    Route::post('/profile/update', [ProfileUserController::class, 'updateProfile']);
    // إنشاء طلب
    Route::post('/orders', [OrderController::class, 'store']);  
    Route::put('/orders/{order}/update', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']); 
    Route::get('/orders/{id}/status', [OrderController::class, 'status']);
    Route::get('/orders/index', [OrderController::class, 'index']); 
    Route::get('/orders/{order}/show', [OrderController::class, 'show']);
    Route::post('/orders/{id}/increase-fee', [OrderController::class, 'increaseFee']);
    Route::post('/drivers/{driver_id}/rate/{order_id}', [RatingController::class, 'rateDriver']);
    Route::post('/orders/{order_id}/reassign-driver', [OrderController::class, 'reassignDriver']);
    Route::prefix('support')->group(function () {
        Route::get('/info', [SupportController::class, 'info']);       // جلب معلومات الدعم
        Route::post('/feedback', [FeedbackController::class, 'store']); // إرسال ملاحظة/شكوى
    });
    Route::get('user/reports/summary', [ReportController::class, 'summary']);
    Route::get('/delivery-pricings', [DeliveryPricingController::class, 'index']);



    Route::prefix('driver')->middleware('active.driver')->group(function () {
        Route::get('/orders', [DriverOrderController::class, 'availableOrders']);
        Route::post('/orders/{orderId}/accept', [DriverOrderController::class, 'acceptOrder']);
        Route::post('/orders/{order}/complain', [DriverOrderController::class, 'complain']);
        Route::get('/orders/current', [DriverOrderController::class, 'currentOrders']);
        Route::post('/orders/{order}/confirm-pickup', [DriverOrderController::class, 'confirmPickup']);
        Route::post('/orders/{order}/complete', [DriverOrderController::class, 'completeOrder']);
    });
    // تحديث موقع السائق الحالي ...
    Route::post('driver/location/update', [DriverLocationController::class, 'update']);

    Route::get('driver/reports/summary', [ReportController::class, 'driver_summary']);
    Route::get('/subscription/summary', [SubscriptionController::class, 'summary'])
        ->name('subscription.summary');

    // يرجع سجل كل الاشتراكات (شهري + باقات) للسائق
    Route::get('/subscription/history', [SubscriptionController::class, 'subscriptionsHistory'])
        ->name('subscription.history');
    // عرض كل الإشعارات للمستخدم الحالي
    Route::get('/notifications', [NotificationController::class, 'index']);

    // وضع إشعار كمقروء
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // حذف إشعار
    Route::delete('/notifications/{id}/destroy', [NotificationController::class, 'destroy']);

    // إرسال إشعار (للأدمن أو للتجربة)
    Route::post('/notifications/send', [NotificationController::class, 'send']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens/{token}', [DeviceTokenController::class, 'destroy']);
    Route::patch('/device-token/{oldToken}', [DeviceTokenController::class, 'update']);
});
require __DIR__ . '/auth.php';
