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
        Schema::create('variants_configurations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['text', 'color'])->nullable();
            $table->string('value')->nullable();
            $table->foreignId('key_id')->nullable()->constrained('variants_configurations_keys')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('variants_configurations')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants_configurations');
    }
};
