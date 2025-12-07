<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\AreaSettings\app\Models\Country;

class VariantConfigurationSeeder extends Seeder
{
    /**
     * Variant keys with their values
     */
    private $variantKeysData = [
        'Color' => [
            'ar' => 'اللون',
            'values' => [
                ['en' => 'Black', 'ar' => 'أسود'],
                ['en' => 'White', 'ar' => 'أبيض'],
                ['en' => 'Red', 'ar' => 'أحمر'],
                ['en' => 'Blue', 'ar' => 'أزرق'],
                ['en' => 'Green', 'ar' => 'أخضر'],
                ['en' => 'Yellow', 'ar' => 'أصفر'],
                ['en' => 'Purple', 'ar' => 'بنفسجي'],
                ['en' => 'Orange', 'ar' => 'برتقالي'],
                ['en' => 'Pink', 'ar' => 'وردي'],
                ['en' => 'Gray', 'ar' => 'رمادي'],
                ['en' => 'Brown', 'ar' => 'بني'],
                ['en' => 'Navy', 'ar' => 'كحلي'],
                ['en' => 'Beige', 'ar' => 'بيج'],
                ['en' => 'Gold', 'ar' => 'ذهبي'],
                ['en' => 'Silver', 'ar' => 'فضي'],
            ]
        ],
        'Size' => [
            'ar' => 'الحجم',
            'values' => [
                ['en' => 'XS', 'ar' => 'XS'],
                ['en' => 'S', 'ar' => 'S'],
                ['en' => 'M', 'ar' => 'M'],
                ['en' => 'L', 'ar' => 'L'],
                ['en' => 'XL', 'ar' => 'XL'],
                ['en' => 'XXL', 'ar' => 'XXL'],
                ['en' => 'XXXL', 'ar' => 'XXXL'],
            ]
        ],
        'Shoe Size' => [
            'ar' => 'مقاس الحذاء',
            'values' => [
                ['en' => '36', 'ar' => '36'],
                ['en' => '37', 'ar' => '37'],
                ['en' => '38', 'ar' => '38'],
                ['en' => '39', 'ar' => '39'],
                ['en' => '40', 'ar' => '40'],
                ['en' => '41', 'ar' => '41'],
                ['en' => '42', 'ar' => '42'],
                ['en' => '43', 'ar' => '43'],
                ['en' => '44', 'ar' => '44'],
                ['en' => '45', 'ar' => '45'],
                ['en' => '46', 'ar' => '46'],
            ]
        ],
        'Material' => [
            'ar' => 'الخامة',
            'values' => [
                ['en' => 'Cotton', 'ar' => 'قطن'],
                ['en' => 'Polyester', 'ar' => 'بوليستر'],
                ['en' => 'Leather', 'ar' => 'جلد'],
                ['en' => 'Wool', 'ar' => 'صوف'],
                ['en' => 'Silk', 'ar' => 'حرير'],
                ['en' => 'Denim', 'ar' => 'دينم'],
                ['en' => 'Linen', 'ar' => 'كتان'],
                ['en' => 'Nylon', 'ar' => 'نايلون'],
            ]
        ],
        'Storage' => [
            'ar' => 'السعة التخزينية',
            'values' => [
                ['en' => '32GB', 'ar' => '32 جيجا'],
                ['en' => '64GB', 'ar' => '64 جيجا'],
                ['en' => '128GB', 'ar' => '128 جيجا'],
                ['en' => '256GB', 'ar' => '256 جيجا'],
                ['en' => '512GB', 'ar' => '512 جيجا'],
                ['en' => '1TB', 'ar' => '1 تيرا'],
                ['en' => '2TB', 'ar' => '2 تيرا'],
            ]
        ],
        'RAM' => [
            'ar' => 'الذاكرة العشوائية',
            'values' => [
                ['en' => '4GB', 'ar' => '4 جيجا'],
                ['en' => '8GB', 'ar' => '8 جيجا'],
                ['en' => '16GB', 'ar' => '16 جيجا'],
                ['en' => '32GB', 'ar' => '32 جيجا'],
                ['en' => '64GB', 'ar' => '64 جيجا'],
            ]
        ],
        'Weight' => [
            'ar' => 'الوزن',
            'values' => [
                ['en' => '250g', 'ar' => '250 جرام'],
                ['en' => '500g', 'ar' => '500 جرام'],
                ['en' => '1kg', 'ar' => '1 كيلو'],
                ['en' => '2kg', 'ar' => '2 كيلو'],
                ['en' => '5kg', 'ar' => '5 كيلو'],
            ]
        ],
        'Flavor' => [
            'ar' => 'النكهة',
            'values' => [
                ['en' => 'Vanilla', 'ar' => 'فانيليا'],
                ['en' => 'Chocolate', 'ar' => 'شوكولاتة'],
                ['en' => 'Strawberry', 'ar' => 'فراولة'],
                ['en' => 'Mint', 'ar' => 'نعناع'],
                ['en' => 'Caramel', 'ar' => 'كراميل'],
            ]
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🎨 Starting Variant Configuration Seeder...\n";

        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        if ($languages->isEmpty()) {
            echo "❌ Error: No languages found. Please run LanguageSeeder first.\n";
            return;
        }

        // Get country from session or use first country
        $countryCode = session('country_code', 'EG');
        $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = \Modules\AreaSettings\app\Models\Country::first();
        }

        $countryId = $country ? $country->id : null;

        if (!$countryId) {
            echo "❌ Error: No country found for code: {$countryCode}\n";
            return;
        }

        echo "✓ Using country: {$country->code} (ID: {$countryId})\n";

        $keysCreated = 0;
        $valuesCreated = 0;

        foreach ($this->variantKeysData as $keyNameEn => $keyData) {
            // Check if key already exists for this country
            $existingKey = VariantConfigurationKey::where('country_id', $countryId)
                ->whereHas('translations', function($q) use ($keyNameEn) {
                    $q->where('lang_key', 'name')->where('lang_value', $keyNameEn);
                })->first();

            if (!$existingKey) {
                // Create the variant key
                $variantKey = VariantConfigurationKey::create([
                    'parent_key_id' => null,
                    'country_id' => $countryId,
                ]);

                foreach ($languages as $langCode => $language) {
                    $variantKey->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $keyNameEn : $keyData['ar'],
                    ]);
                }

                echo "  ✓ Created key: {$keyNameEn} (ID: {$variantKey->id})\n";
                $keysCreated++;
            } else {
                $variantKey = $existingKey;
                echo "  ⏭️ Key exists: {$keyNameEn} (ID: {$variantKey->id})\n";
            }

            // Create values for this key
            echo "    Creating values for key ID: {$variantKey->id}\n";
            foreach ($keyData['values'] as $valueData) {
                // Check if value already exists for this key
                $existingValue = VariantsConfiguration::where('key_id', $variantKey->id)
                    ->whereHas('translations', function($q) use ($valueData) {
                        $q->where('lang_key', 'name')->where('lang_value', $valueData['en']);
                    })->first();

                if (!$existingValue) {
                    try {
                        $config = VariantsConfiguration::create([
                            'key_id' => $variantKey->id,
                            'country_id' => $countryId,
                            'parent_id' => null,
                        ]);

                        foreach ($languages as $langCode => $language) {
                            $config->translations()->create([
                                'lang_id' => $language->id,
                                'lang_key' => 'name',
                                'lang_value' => $langCode === 'en' ? $valueData['en'] : $valueData['ar'],
                            ]);
                        }

                        echo "    ✓ Created value: {$valueData['en']} (ID: {$config->id}, Key ID: {$config->key_id})\n";
                        $valuesCreated++;
                    } catch (\Exception $e) {
                        echo "    ❌ Error creating value {$valueData['en']}: {$e->getMessage()}\n";
                    }
                } else {
                    echo "    ⏭️ Value exists: {$valueData['en']} (ID: {$existingValue->id})\n";
                }
            }
        }

        echo "\n✅ Variant Configuration Seeder completed!\n";
        echo "   Keys created: {$keysCreated}\n";
        echo "   Values created: {$valuesCreated}\n";
    }
}
