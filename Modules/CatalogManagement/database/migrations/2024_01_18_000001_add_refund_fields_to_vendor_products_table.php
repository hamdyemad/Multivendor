<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->boolean('is_able_to_refund')->default(false)->after('is_active')->comment('Product can be refunded');
            $table->integer('refund_days')->nullable()->after('is_able_to_refund')->comment('Days after delivery to request refund');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->dropColumn(['is_able_to_refund', 'refund_days']);
        });
    }
};
