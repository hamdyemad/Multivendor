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
        Schema::create('permessions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['admin', 'other'])->default('other');
            $table->string('key', 100)->unique();
            $table->string('group_by', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permessions');
    }
};
