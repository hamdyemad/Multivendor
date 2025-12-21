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
        Schema::table('permessions', function (Blueprint $table) {
            // Drop old type enum and recreate with new values
            $table->dropColumn('type');
        });
        
        Schema::table('permessions', function (Blueprint $table) {
            // Add new columns
            $table->enum('type', ['admin', 'all'])->default('admin')->after('id');
            $table->string('name_en')->after('key');
            $table->string('name_ar')->after('name_en');
            $table->string('module')->after('type')->index();
            $table->string('sub_module')->after('module')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permessions', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar', 'module', 'sub_module']);
            $table->dropColumn('type');
        });
        
        Schema::table('permessions', function (Blueprint $table) {
            $table->enum('type', ['admin', 'other'])->default('other');
        });
    }
};
