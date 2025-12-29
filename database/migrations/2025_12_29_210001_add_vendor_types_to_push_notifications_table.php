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
        // Modify the type enum to include vendor types
        DB::statement("ALTER TABLE push_notifications MODIFY COLUMN type ENUM('all', 'specific', 'all_vendors', 'specific_vendors') DEFAULT 'all'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE push_notifications MODIFY COLUMN type ENUM('all', 'specific') DEFAULT 'all'");
    }
};
