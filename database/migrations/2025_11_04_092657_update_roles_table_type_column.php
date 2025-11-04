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
        // Update the type column to include new enum values: super_admin, admin, vendor, other
        DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('super_admin', 'admin', 'vendor', 'other') DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('admin', 'other') DEFAULT 'other'");
    }
};
