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
        Schema::table('vendor_order_stages', function (Blueprint $table) {
            $table->decimal('promo_code_share', 10, 2)->default(0)->after('stage_id')
                ->comment('Vendor share of promo code discount (distributed equally)');
            $table->decimal('points_share', 10, 2)->default(0)->after('promo_code_share')
                ->comment('Vendor share of points discount (distributed equally)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_order_stages', function (Blueprint $table) {
            $table->dropColumn(['promo_code_share', 'points_share']);
        });
    }
};
