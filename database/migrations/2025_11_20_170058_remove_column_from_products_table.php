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
            $table->dropColumn('has_offer');
            $table->dropColumn('offer_end_date');
        });
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            $table->date('discount_end_date')->after('price_before_discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            $table->boolean('has_offer')->default(false);
            $table->date('offer_end_date')->nullable();
        });
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            $table->dropColumn('discount_end_date');
        });
    }
};
