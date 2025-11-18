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
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'slug')) {
                $table->string('slug')->unique()->after('id');
            }
            if (!Schema::hasColumn('product_variants', 'video_link')) {
                $table->string('video_link')->nullable()->after('slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('product_variants', 'video_link')) {
                $table->dropColumn('video_link');
            }
        });
    }
};
