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
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->integer('sort_number')->default(0)->after('is_active');
            $table->index('sort_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->dropIndex(['sort_number']);
            $table->dropColumn('sort_number');
        });
    }
};
