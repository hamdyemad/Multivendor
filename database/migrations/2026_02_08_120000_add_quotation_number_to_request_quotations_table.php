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
        Schema::table('request_quotations', function (Blueprint $table) {
            $table->string('quotation_number')->nullable()->unique()->after('id');
        });

        // Generate quotation numbers for existing records
        $this->generateExistingQuotationNumbers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_quotations', function (Blueprint $table) {
            $table->dropColumn('quotation_number');
        });
    }

    /**
     * Generate quotation numbers for existing records
     */
    private function generateExistingQuotationNumbers(): void
    {
        $quotations = DB::table('request_quotations')
            ->whereNull('quotation_number')
            ->orderBy('id')
            ->get();

        foreach ($quotations as $index => $quotation) {
            $number = $index + 1;
            $quotationNumber = 'RQ-' . str_pad($number, 6, '0', STR_PAD_LEFT);
            
            DB::table('request_quotations')
                ->where('id', $quotation->id)
                ->update(['quotation_number' => $quotationNumber]);
        }
    }
};
