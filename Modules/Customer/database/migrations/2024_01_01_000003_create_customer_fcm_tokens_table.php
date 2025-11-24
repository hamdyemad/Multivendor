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
        // Skip this migration - fixed in 2024_01_01_000004_fix_customer_fcm_tokens_table
        if (Schema::hasTable('customer_fcm_tokens')) {
            return;
        }

        Schema::create('customer_fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('fcm_token', 500); // FCM tokens are typically ~150-200 chars
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->timestamps();

            $table->index(['customer_id']);
            $table->unique(['customer_id', 'fcm_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_fcm_tokens');
    }
};
