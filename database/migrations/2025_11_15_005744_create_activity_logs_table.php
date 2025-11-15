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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 50); // created, updated, deleted, restored, login, logout, etc.
            $table->string('model')->nullable(); // Model class name
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->string('description_key')->nullable(); // e.g., 'activity_log.created_country'
            $table->json('description_params')->nullable(); // Parameters for translation
            $table->json('properties')->nullable(); // Old/new values, additional data
            $table->string('ip_address', 45)->nullable(); // IPv6 support
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['model', 'model_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
