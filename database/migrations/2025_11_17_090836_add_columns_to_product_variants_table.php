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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_key_id')->nullable()->after('id');
            $table->unsignedBigInteger('variant_value_id')->nullable()->after('id');

            // Add foreign key constraints with explicit table names
            $table->foreign('variant_key_id')
                ->references('id')
                ->on('variants_configurations_keys')
                ->cascadeOnDelete();

            $table->foreign('variant_value_id')
                ->references('id')
                ->on('variants_configurations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['variant_key_id']);
            $table->dropForeign(['variant_value_id']);

            // Then drop columns
            $table->dropColumn('variant_key_id');
            $table->dropColumn('variant_value_id');
        });
    }
};
