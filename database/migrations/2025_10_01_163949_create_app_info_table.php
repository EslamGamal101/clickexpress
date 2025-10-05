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
        Schema::create('app_info', function (Blueprint $table) {
            $table->id();
            $table->text('terms')->nullable();      
            $table->string('whatsapp')->nullable();  
            $table->string('facebook')->nullable();  
            $table->string('instagram')->nullable(); 
            $table->text('about_us')->nullable();    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_info');
    }
};
