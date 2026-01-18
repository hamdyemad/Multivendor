<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('refund_number')->unique()->comment('REF-YYYYMMDD-XXXX');
            $table->enum('status', ['pending', 'approved', 'in_progress', 'picked_up', 'refunded', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('total_products_amount', 10, 2)->default(0)->comment('Sum of refunded products');
            $table->decimal('total_shipping_amount', 10, 2)->default(0)->comment('Shipping refund amount');
            $table->decimal('total_tax_amount', 10, 2)->default(0)->comment('Tax refund amount');
            $table->decimal('total_discount_amount', 10, 2)->default(0)->comment('Discount reversed');
            $table->decimal('return_shipping_cost', 10, 2)->default(0)->comment('Cost customer pays for return');
            $table->integer('points_to_deduct')->default(0)->comment('Points earned from order to deduct');
            $table->decimal('points_value_deducted', 10, 2)->default(0)->comment('Value of points used in order');
            $table->decimal('total_refund_amount', 10, 2)->default(0)->comment('Final amount to refund');
            $table->text('reason')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('refund_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
