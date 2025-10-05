<?php
use App\Http\Controllers\Auth\AdminLoginController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'guest'], function () {
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');

});
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth');
