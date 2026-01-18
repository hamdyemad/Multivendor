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
        Schema::table('refund_requests', function (Blueprint $table) {
            // Add parent_refund_id to link vendor refunds to customer's main refund
            $table->foreignId('parent_refund_id')
                ->nullable()
                ->after('id')
                ->constrained('refund_requests')
                ->onDelete('cascade')
                ->comment('Links vendor refund requests to the main customer refund request');
            
            // Add is_parent flag to identify the main customer refund
            $table->boolean('is_parent')
                ->default(false)
                ->after('parent_refund_id')
                ->comment('True if this is the main customer refund request');
            
            $table->index('parent_refund_id');
            $table->index('is_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign(['parent_refund_id']);
            $table->dropIndex(['parent_refund_id']);
            $table->dropIndex(['is_parent']);
            $table->dropColumn(['parent_refund_id', 'is_parent']);
        });
    }
};
