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
        // Check if foreign key exists and drop it
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'products'
            AND CONSTRAINT_NAME = 'products_sub_category_id_foreign'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        if (!empty($foreignKeyExists)) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['sub_category_id']);
            });
        }

        // Recreate the foreign key to reference sub_categories table
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('sub_category_id')
                  ->references('id')
                  ->on('sub_categories')
                  ->onDelete('cascade');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
        });

        // Restore the old foreign key to categories table
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('sub_category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};
