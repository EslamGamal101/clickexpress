<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('customer', 'driver', 'vendor', 'admin', 'management_producers') NOT NULL");
    }

    public function down(): void
    {
        // ترجع للوضع القديم لو عملت rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('customer', 'driver', 'vendor', 'admin') NOT NULL");
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
