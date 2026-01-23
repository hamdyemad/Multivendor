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
        Schema::create('vendor_order_stage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_order_stage_id')->constrained('vendor_order_stages')->onDelete('cascade');
            $table->foreignId('old_stage_id')->nullable()->constrained('order_stages')->onDelete('cascade');
            $table->foreignId('new_stage_id')->constrained('order_stages')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_order_stage_histories');
    }
};
