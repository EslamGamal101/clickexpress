<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('rate_driver')->nullable();
            $table->tinyInteger('rate_app')->nullable(); 
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
