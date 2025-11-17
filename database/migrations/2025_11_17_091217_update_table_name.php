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
        if (Schema::hasTable('variant_stocks') && !Schema::hasTable('product_variant_stocks')) {
            Schema::rename('variant_stocks', 'product_variant_stocks');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::rename('product_variant_stocks', 'variant_stocks');
    }
};
