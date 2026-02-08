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
        // Modify the status column to include new statuses
        DB::statement("ALTER TABLE request_quotations MODIFY COLUMN status ENUM(
            'pending',
            'sent_to_vendors',
            'offers_received',
            'partially_accepted',
            'fully_accepted',
            'rejected',
            'sent_offer',
            'accepted_offer',
            'rejected_offer',
            'order_created',
            'orders_created',
            'archived'
        ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old statuses
        DB::statement("ALTER TABLE request_quotations MODIFY COLUMN status ENUM(
            'pending',
            'sent_offer',
            'accepted_offer',
            'rejected_offer',
            'order_created',
            'archived'
        ) NOT NULL DEFAULT 'pending'");
    }
};
