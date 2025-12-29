<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, find and drop all foreign keys on this table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_product_taxes' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `order_product_taxes` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
        
        // Now drop the unique index
        DB::statement('ALTER TABLE `order_product_taxes` DROP INDEX `order_product_taxes_order_product_id_tax_id_unique`');
        
        // Re-add the foreign keys
        Schema::table('order_product_taxes', function (Blueprint $table) {
            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product_taxes', function (Blueprint $table) {
            $table->unique(['order_product_id', 'tax_id']);
        });
    }
};
