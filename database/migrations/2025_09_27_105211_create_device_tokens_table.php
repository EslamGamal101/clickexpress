<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();

            // صاحب الجهاز (User أو Driver)
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type'); // 'user' or 'driver'

            // بيانات الجهاز
            $table->string('token')->unique(); // Firebase token
            $table->string('platform')->nullable(); // android, ios, web
            $table->boolean('is_active')->default(true);

            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
