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
        Schema::table('request_quotations', function (Blueprint $table) {
            $table->dropColumn(['offer_price', 'offer_notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_quotations', function (Blueprint $table) {
            $table->decimal('offer_price', 10, 2)->nullable()->after('file');
            $table->text('offer_notes')->nullable()->after('offer_price');
        });
    }
};
