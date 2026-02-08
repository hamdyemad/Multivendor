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
        Schema::create('request_quotation_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_quotation_id')->constrained('request_quotations')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->enum('status', ['pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'order_created'])->default('pending');
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->text('offer_notes')->nullable();
            $table->timestamp('offer_sent_at')->nullable();
            $table->timestamp('offer_responded_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();
            
            // Unique constraint: one vendor per quotation
            $table->unique(['request_quotation_id', 'vendor_id'], 'unique_quotation_vendor');
            
            // Indexes for performance
            $table->index('status');
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_quotation_vendors');
    }
};
