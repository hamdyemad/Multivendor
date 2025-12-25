<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM to include 'allocated' status
        DB::statement("ALTER TABLE stock_bookings MODIFY COLUMN status ENUM('booked', 'allocated', 'released', 'fulfilled') DEFAULT 'booked'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM (first update any 'allocated' to 'booked')
        DB::statement("UPDATE stock_bookings SET status = 'booked' WHERE status = 'allocated'");
        DB::statement("ALTER TABLE stock_bookings MODIFY COLUMN status ENUM('booked', 'released', 'fulfilled') DEFAULT 'booked'");
    }
};
