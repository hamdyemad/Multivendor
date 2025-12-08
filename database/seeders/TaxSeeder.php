<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\AreaSettings\app\Models\Country;

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


        // Get country_id from session
        $countryCode = session('country_code', 'EG');
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = Country::first();
        }

        $countryId = $country ? $country->id : null;


        foreach ($this->taxesData as $taxData) {
            // Check if tax with this rate already exists (primary check)
            $existingTax = Tax::where('tax_rate', $taxData['rate'])->first();

            if ($existingTax) {
                echo "  ⏭️ Skipped: {$taxData['en']} (already exists with rate {$taxData['rate']}%)\n";
                $skipped++;
                continue;
            }

            // Generate unique slug (globally unique)
            $slug = \Illuminate\Support\Str::slug($taxData['en']);
            $counter = 1;
            $originalSlug = $slug;

            // Keep incrementing counter until we find a unique slug
            while (Tax::where('slug', $slug)
                ->withoutCountryFilter()->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            try {
                $tax = Tax::create([
                    'country_id' => $countryId,
                    'slug' => $slug,
                    'tax_rate' => $taxData['rate'],
                    'active' => true,
                ]);

                echo "  ✓ Created: {$taxData['en']} ({$taxData['rate']}%)\n";
                $created++;
            } catch (\Exception $e) {
                echo "  ⏭️ Skipped: {$taxData['en']} (already exists)\n";
                $skipped++;
                continue;
            }

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
