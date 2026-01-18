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
        // Check if column exists first
        if (!Schema::hasColumn('order_products', 'shipping_cost')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_products', 'shipping_cost')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->dropColumn('shipping_cost');
            });
        }
    }
};
