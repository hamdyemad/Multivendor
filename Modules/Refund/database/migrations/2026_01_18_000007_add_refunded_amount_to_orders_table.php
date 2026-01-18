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
        // Check if column exists first (already exists in Order model fillable)
        if (!Schema::hasColumn('orders', 'refunded_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('refunded_amount', 10, 2)->default(0)->after('total_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'refunded_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('refunded_amount');
            });
        }
    }
};
