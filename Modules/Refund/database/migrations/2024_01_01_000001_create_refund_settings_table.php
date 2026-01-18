<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('customer_pays_return_shipping')->default(false)->comment('Customer pays return shipping');
            $table->integer('refund_processing_days')->default(7)->comment('Default days to process refund');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('refund_settings')->insert([
            'customer_pays_return_shipping' => false,
            'refund_processing_days' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_settings');
    }
};
