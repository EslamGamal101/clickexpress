<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('target_type', ['all_drivers', 'specific_driver', 'specific_package']);
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('cascade');
            $table->string('title')->nullable(); 
            $table->string('occasion')->nullable(); 
            $table->enum('discount_type', ['percentage', 'amount'])->default('percentage');
            $table->decimal('value', 8, 2); 
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
