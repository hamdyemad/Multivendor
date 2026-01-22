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
        Schema::table('refund_request_items', function (Blueprint $table) {
            $table->decimal('commission', 5, 2)->default(0)->after('refund_amount')
                ->comment('Commission percentage from order_products at time of refund');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_request_items', function (Blueprint $table) {
            $table->dropColumn('commission');
        });
    }
};
