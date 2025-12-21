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
        if (!Schema::hasColumn('vendor_products', 'video_link')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->string('video_link')->nullable()->after('sku');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('vendor_products', 'video_link')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->dropColumn('video_link');
            });
        }
    }
};
