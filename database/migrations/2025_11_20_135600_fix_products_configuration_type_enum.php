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
        // First, update any existing 'with_variants' values to 'variants'
        DB::table('products')
            ->where('configuration_type', 'with_variants')
            ->update(['configuration_type' => 'variants']);

        // Then modify the enum to use the correct values
        DB::statement("ALTER TABLE products MODIFY COLUMN configuration_type ENUM('simple', 'variants') DEFAULT 'simple'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any existing 'variants' values back to 'with_variants'
        DB::table('products')
            ->where('configuration_type', 'variants')
            ->update(['configuration_type' => 'with_variants']);

        // Then modify the enum back to the original values
        DB::statement("ALTER TABLE products MODIFY COLUMN configuration_type ENUM('simple', 'with_variants') DEFAULT 'simple'");
    }
};
