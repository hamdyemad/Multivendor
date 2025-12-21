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
            $table->string('module_icon')->nullable()->after('sub_module');
            $table->string('color')->nullable()->after('module_icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permessions', function (Blueprint $table) {
            $table->dropColumn(['module_icon', 'color']);
        });
    }
};
