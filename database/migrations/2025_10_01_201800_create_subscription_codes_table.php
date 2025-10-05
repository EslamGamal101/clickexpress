<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // الكود نفسه
            $table->unsignedBigInteger('package_id'); // الباقة المرتبط بيها الكود
            $table->boolean('is_used')->default(false); // هل الكود مستخدم
            $table->unsignedBigInteger('used_by')->nullable(); // السائق اللي استخدم الكود
            $table->timestamp('used_at')->nullable(); // وقت الاستخدام
            $table->timestamp('expires_at')->nullable(); // صلاحية الكود
            $table->timestamps();

            // العلاقات
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('used_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_codes');
    }
};
