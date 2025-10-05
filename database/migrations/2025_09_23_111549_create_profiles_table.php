<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // الاسم
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // بيانات عامة
            $table->string('national_id')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();

            // للمتاجر (vendors)
            $table->string('vendor_name')->nullable();

            // للسائقين (drivers)
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('license_image')->nullable();
            $table->string('car_image')->nullable();

            // صورة البروفايل
            $table->string('profile_image')->nullable();
            $table->string('id_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
