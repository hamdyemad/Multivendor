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
        // This migration is a no-op since commission is already added by other migrations
        // Just check if the column exists, if not add it
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'commission')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->decimal('commission', 8, 2)->nullable()->default(0)->after('country_id')->comment('Commission percentage for this department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op reverse since this is a safety check migration
    }
};
