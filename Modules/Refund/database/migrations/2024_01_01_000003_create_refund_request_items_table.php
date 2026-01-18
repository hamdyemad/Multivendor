<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_request_id')->constrained('refund_requests')->onDelete('cascade');
            $table->foreignId('order_product_id')->constrained('order_products')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('vendor_product_variants')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0)->comment('Original unit price');
            $table->decimal('total_price', 10, 2)->default(0)->comment('quantity * unit_price');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Tax for this item');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Discount applied to this item');
            $table->decimal('refund_amount', 10, 2)->default(0)->comment('Final refund for this item');
            $table->enum('vendor_status', ['pending', 'approved', 'in_progress', 'picked_up', 'refunded', 'rejected'])->default('pending');
            $table->text('vendor_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index('refund_request_id');
            $table->index('order_product_id');
            $table->index('vendor_id');
            $table->index('vendor_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_request_items');
    }
};
