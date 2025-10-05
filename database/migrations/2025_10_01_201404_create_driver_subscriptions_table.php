<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_subscriptions', function (Blueprint $table) {
            $table->id();

            // ربط بالسائق (users)
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');

            // ربط بالباقة (packages)
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');

            $table->timestamp('activated_at')->nullable();  // وقت التفعيل
            $table->timestamp('expires_at')->nullable();    // تاريخ انتهاء الاشتراك

            $table->integer('remaining_rides')->default(0); // عدد الرحلات المتبقية

            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_subscriptions');
    }
};
