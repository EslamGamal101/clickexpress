<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الباقة
            $table->decimal('price', 8, 2); // سعر الباقة (مثال: 4.00 JD)
            $table->integer('rides_count'); // عدد الرحلات
            $table->integer('duration_days')->nullable(); // مدة الباقة بالأيام (اختياري)
            $table->boolean('is_active')->default(true); // هل الباقة مفعلة؟
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
