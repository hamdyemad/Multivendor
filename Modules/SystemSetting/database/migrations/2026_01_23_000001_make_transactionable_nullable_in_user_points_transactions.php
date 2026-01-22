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
        Schema::table('user_points_transactions', function (Blueprint $table) {
            // Make transactionable fields nullable for manual adjustments
            $table->unsignedBigInteger('transactionable_id')->nullable()->change();
            $table->string('transactionable_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points_transactions', function (Blueprint $table) {
            // Revert to non-nullable
            $table->unsignedBigInteger('transactionable_id')->nullable(false)->change();
            $table->string('transactionable_type')->nullable(false)->change();
        });
    }
};
