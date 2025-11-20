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
        // Skip this migration as it's handled by the fix migration
        // This prevents conflicts and duplicate foreign key constraints
        return;
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
