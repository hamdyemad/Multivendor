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
        // Change payment_type from ENUM to VARCHAR to support more payment methods
        // Using raw SQL to avoid doctrine/dbal dependency issues with ENUMs
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_type VARCHAR(255) NOT NULL DEFAULT 'cash_on_delivery'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (Warning: Data might be truncated if it contains values not in this list)
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_type ENUM('cash_on_delivery', 'online') NOT NULL DEFAULT 'cash_on_delivery'");
    }
};
