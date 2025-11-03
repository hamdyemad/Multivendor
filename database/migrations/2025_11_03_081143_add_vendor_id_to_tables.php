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
        Schema::table('activities', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
        Schema::table('taxes', function (Blueprint $table) {
            $table->string('vendor_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
