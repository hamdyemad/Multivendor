<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->unsignedBigInteger('sub_category_id')->nullable()->change();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->unsignedBigInteger('sub_category_id')->nullable(false)->change();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
};
