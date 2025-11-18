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
        Schema::table('products', function (Blueprint $table) {
            // Only add the column if it doesn't exist
            if (!Schema::hasColumn('products', 'sub_category_id')) {
                $table->foreignId('sub_category_id')->constrained('sub_categories')->cascadeOnDelete();
            } else {
                // Column exists, check if we need to update the constraint
                // First, try to drop existing constraint if it exists
                try {
                    $table->dropForeign(['sub_category_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }

                // Now add the correct constraint
                try {
                    $table->foreign('sub_category_id')->references('id')->on('sub_categories')->cascadeOnDelete();
                } catch (\Exception $e) {
                    // Constraint might already be correct, ignore the error
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
        });
    }
};
