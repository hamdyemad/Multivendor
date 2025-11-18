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
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            // Only add the column if it doesn't exist
            if (!Schema::hasColumn('vendor_product_variants', 'variant_configuration_id')) {
                $table->foreignId('variant_configuration_id')->after('vendor_product_id')->nullable()->constrained('variants_configurations')->nullOnDelete();
            } else {
                // Column exists, check if we need to update the constraint
                // First, try to drop existing constraint if it exists
                try {
                    $table->dropForeign(['variant_configuration_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }

                // Now add the correct constraint
                try {
                    $table->foreign('variant_configuration_id')->after('vendor_product_id')->nullable()->references('id')->on('variants_configurations')->nullOnDelete();
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
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            $table->dropForeign(['variant_configuration_id']);
            $table->dropColumn('variant_configuration_id');
        });
    }
};
