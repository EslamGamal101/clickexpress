<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->nullable();
            $table->string('serial_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('order_type', ['package', 'cargo']); // طرد / حمولة
            $table->enum('delivery_type', ['instant', 'scheduled']);
            $table->date('delivery_date')->nullable()->after('delivery_type');
            $table->string('package_type')->nullable();
            $table->string('package_other')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('assign_last_driver')->default(false);
            $table->enum('status', ['pending', 'accepted', 'picked_up', 'delivered', 'cancelled'])->default('pending');
            $table->string('vehicle_type')->nullable(); // نوع المركبة المطلوبة للطلب
            $table->string('cancellation_reason')->nullable();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
