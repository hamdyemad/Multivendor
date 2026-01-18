<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add vendor fields to refund_requests table
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->foreignId('vendor_id')->after('customer_id')->constrained('vendors')->onDelete('cascade');
            $table->enum('vendor_status', ['pending', 'approved', 'in_progress', 'picked_up', 'refunded', 'rejected'])
                ->after('status')
                ->default('pending')
                ->comment('Vendor-specific status for tracking');
            $table->text('vendor_notes')->after('customer_notes')->nullable();
            
            $table->index('vendor_id');
            $table->index('vendor_status');
        });

        // Remove vendor fields from refund_request_items table
        Schema::table('refund_request_items', function (Blueprint $table) {
            $table->dropColumn(['vendor_status', 'vendor_notes', 'approved_at', 'refunded_at']);
        });
    }

    public function down(): void
    {
        // Add vendor fields back to refund_request_items table
        Schema::table('refund_request_items', function (Blueprint $table) {
            $table->enum('vendor_status', ['pending', 'approved', 'in_progress', 'picked_up', 'refunded', 'rejected'])->default('pending');
            $table->text('vendor_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
        });

        // Remove vendor fields from refund_requests table
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['vendor_status']);
            $table->dropColumn(['vendor_id', 'vendor_status', 'vendor_notes']);
        });
    }
};
