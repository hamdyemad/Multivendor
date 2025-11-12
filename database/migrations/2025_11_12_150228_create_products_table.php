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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('sku');
            $table->integer('points')->nullable();
            $table->enum('is_active', [1, 0])->default(1);
            $table->enum('is_featured', [1, 0])->default(0);
            $table->foreignId('brand_id')->nullable()->constrained('brands');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories');
            $table->foreignId('tax_id')->nullable()->constrained('taxes');
            $table->integer('max_per_order')->nullable();
            $table->string('video_link')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
