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
            $table->decimal('points_per_currency', 10, 2)->default(0)->after('points')
                ->comment('Points earned per currency unit at the time of transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points_transactions', function (Blueprint $table) {
            $table->dropColumn('points_per_currency');
        });
    }
};
