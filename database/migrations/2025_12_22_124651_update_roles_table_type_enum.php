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
        // Check if type column exists before trying to modify it
        if (Schema::hasColumn('roles', 'type')) {
            // For MySQL, we need to use raw SQL to modify ENUM
            DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('super_admin', 'admin', 'vendor', 'vendor_user', 'users_vendors', 'other') DEFAULT 'other'");
        } else {
            // Add the type column if it doesn't exist
            Schema::table('roles', function (Blueprint $table) {
                $table->enum('type', ['super_admin', 'admin', 'vendor', 'vendor_user', 'users_vendors', 'other'])->default('other')->after('vendor_id');
            });
        }
        
        // Add is_system_protected column if it doesn't exist
        if (!Schema::hasColumn('roles', 'is_system_protected')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_system_protected')->default(false)->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Modify type column back to original values if it exists
        if (Schema::hasColumn('roles', 'type')) {
            DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('admin', 'other') DEFAULT 'other'");
        }
        
        // Drop is_system_protected if it exists
        if (Schema::hasColumn('roles', 'is_system_protected')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('is_system_protected');
            });
        }
    }
};
