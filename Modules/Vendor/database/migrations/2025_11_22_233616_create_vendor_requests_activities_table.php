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
        Schema::create('vendor_requests_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_request_id')->constrained('vendor_requests')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate entries
            $table->unique(['vendor_request_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_requests_activities');
    }
};
