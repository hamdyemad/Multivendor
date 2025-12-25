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
        Schema::table('stock_bookings', function (Blueprint $table) {
            $table->timestamp('allocated_at')->nullable()->after('booked_at');
            $table->foreignId('allocated_region_id')->nullable()->after('region_id')->constrained('regions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('stock_bookings', 'allocated_region_id')) {
                $table->dropConstrainedForeignId('allocated_region_id');
            }
            if (Schema::hasColumn('stock_bookings', 'allocated_at')) {
                $table->dropColumn('allocated_at');
            }
        });
    }
};
