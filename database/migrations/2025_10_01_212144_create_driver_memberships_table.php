<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->decimal('price', 8, 2); // قيمة الاشتراك الشهري
            $table->timestamp('started_at')->nullable(); // وقت بداية الاشتراك
            $table->timestamp('expires_at')->nullable(); // وقت انتهاء الاشتراك
            $table->boolean('is_active')->default(true); // حالة الاشتراك
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_memberships');
    }
};
