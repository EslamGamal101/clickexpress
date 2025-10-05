<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\web\AdminController;
use App\Http\Controllers\web\AdminDriverController;
use App\Http\Controllers\web\OrderController;
use Illuminate\Support\Facades\Route;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Users Management
Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::resource('users', App\Http\Controllers\web\UsersController::class);
        Route::patch('users/{user}/ban', [App\Http\Controllers\web\UsersController::class, 'toggleStatus'])->name('users.ban');
        Route::patch('users/{user}/unban', [App\Http\Controllers\web\UsersController::class, 'toggleStatus'])->name('users.unban');
        Route::get('users/{id}/download-pdf', [App\Http\Controllers\web\UsersController::class, 'exportPdf'])
            ->name('users.download_pdf');
        Route::prefix('drivers')->as('drivers.')->group(function () {
            Route::get('/', [AdminDriverController::class, 'index'])->name('index');
            Route::get('create', [AdminDriverController::class, 'create'])->name('create');
            Route::post('/', [AdminDriverController::class, 'store'])->name('store');
            Route::get('{driver}', [AdminDriverController::class, 'show'])->name('show');
            Route::get('{driver}/edit', [AdminDriverController::class, 'edit'])->name('edit');
            Route::put('{driver}', [AdminDriverController::class, 'update'])->name('update');
            Route::delete('{driver}', [AdminDriverController::class, 'destroy'])->name('destroy');
            Route::patch('{driver}/ban', [AdminDriverController::class, 'toggleBan'])->name('toggle_ban');
            Route::post('{driver}/notify', [AdminDriverController::class, 'sendNotification'])->name('send_notification');
            Route::post('{driver}/renew-sub', [AdminDriverController::class, 'renewSubscription'])->name('renew_subscription');
            Route::post('{driver}/verify', [AdminDriverController::class, 'verify'])->name('verify');
            Route::post('{driver}/download-pdf', [AdminDriverController::class, 'downloadPdf'])->name('download_pdf');
            Route::post('activate-monthly', [AdminDriverController::class, 'activateMonthly'])->name('activate_monthly');

            // لتفعيل باقة الرحلات (DriverSubscription)
            Route::post('activate-rides-package', [AdminDriverController::class, 'activateRidesPackage'])->name('activate_rides_package');

            // لإنهاء أي اشتراك فعال (سواء شهري أو باقة)
            Route::delete('drivers/{driver}/terminate-subscription/{type}', [AdminDriverController::class, 'terminateSubscription'])
                ->name('terminate_subscription');
        });
        // Orders Management
        Route::resource('orders', OrderController::class);
        Route::patch('orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        // Admins Management
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/', [AdminController::class, 'store'])->name('store');
        Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('destroy');
        // Discounts Management
        Route::resource('discounts', App\Http\Controllers\web\DiscountController::class);
    });
    Route::get('pending', [AdminDriverController::class, 'pending'])->name('pending');
    Route::patch('{driver}/approve', [AdminDriverController::class, 'approve'])->name('approve');
    Route::patch('{driver}/reject', [AdminDriverController::class, 'reject'])->name('reject');
    Route::post('/admin/notifications/send', [NotificationController::class, 'sendFromAdmin'])
        ->name('admin.notifications.send');
    Route::get('/admin/notifications/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
});

require __DIR__ . '/authweb.php';
