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
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn(['is_refund', 'refunded_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->boolean('is_refund')->default(false)->after('quantity');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('is_refund');
        });
    }
};
