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
        Schema::table('products', function (Blueprint $table) {
            // Change enum columns to boolean
            $table->boolean('is_active')->default(true)->change();
            $table->boolean('is_featured')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('is_active', [1, 0])->default(1)->change();
            $table->enum('is_featured', [1, 0])->default(0)->change();
        });
    }
};
