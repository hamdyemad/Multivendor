<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use Modules\CatalogManagement\app\Models\Tax;

class TaxSeeder extends Seeder
{
    /**
     * Taxes data with translations
     */
    private $taxesData = [
        ['rate' => 15, 'en' => 'VAT 15%', 'ar' => 'ضريبة القيمة المضافة 15%'],
        ['rate' => 14, 'en' => 'VAT 14%', 'ar' => 'ضريبة القيمة المضافة 14%'],
        ['rate' => 10, 'en' => 'VAT 10%', 'ar' => 'ضريبة القيمة المضافة 10%'],
        ['rate' => 5, 'en' => 'VAT 5%', 'ar' => 'ضريبة القيمة المضافة 5%'],
        ['rate' => 0, 'en' => 'No Tax', 'ar' => 'بدون ضريبة'],
        ['rate' => 20, 'en' => 'Luxury Tax 20%', 'ar' => 'ضريبة الرفاهية 20%'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n💰 Starting Tax Seeder...\n";

        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        if ($languages->isEmpty()) {
            echo "❌ Error: No languages found. Please run LanguageSeeder first.\n";
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($this->taxesData as $taxData) {
            // Check if tax with this rate already exists
            $existingTax = Tax::where('rate', $taxData['rate'])->first();

            if ($existingTax) {
                echo "  ⏭️ Skipped: {$taxData['en']} (already exists)\n";
                $skipped++;
                continue;
            }

            $tax = Tax::create([
                'rate' => $taxData['rate'],
                'active' => true,
            ]);

            foreach ($languages as $langCode => $language) {
                $tax->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $langCode === 'en' ? $taxData['en'] : $taxData['ar'],
                ]);
            }

            echo "  ✓ Created: {$taxData['en']} ({$taxData['rate']}%)\n";
            $created++;
        }

        echo "\n✅ Tax Seeder completed! Created: {$created}, Skipped: {$skipped}\n";
    }
}
