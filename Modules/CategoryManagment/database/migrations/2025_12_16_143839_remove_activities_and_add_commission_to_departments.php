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
        Schema::disableForeignKeyConstraints();

        // Drop vendors_activities pivot table (from Vendor module, but dependent on activities)
        Schema::dropIfExists('vendors_activities');

        // Drop activities_departments pivot table
        Schema::dropIfExists('activities_departments');
        
        // Drop activities table
        Schema::dropIfExists('activities');

        Schema::enableForeignKeyConstraints();
        
        // Add commission column to departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->decimal('commission', 5, 2)->default(0)->after('active')->comment('Commission percentage for this department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove commission column from departments
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('commission');
        });
        
        // Recreate activities table (basic structure for rollback)
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        
        // Create activities_departments pivot table
        Schema::create('activities_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->timestamps();
        });

        // Recreate vendors_activities pivot table (basic structure)
        if (!Schema::hasTable('vendors_activities')) {
               Schema::create('vendors_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                 $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            }); 
        }
    }
};
