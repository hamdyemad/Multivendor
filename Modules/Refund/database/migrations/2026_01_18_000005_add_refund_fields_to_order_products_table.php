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
            $table->boolean('is_refunded')->default(false)->after('quantity');
            $table->decimal('refunded_amount', 10, 2)->nullable()->after('is_refunded');
            $table->timestamp('refunded_at')->nullable()->after('refunded_amount');
            
            $table->index('is_refunded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex(['is_refunded']);
            $table->dropColumn(['is_refunded', 'refunded_amount', 'refunded_at']);
        });
    }
};
