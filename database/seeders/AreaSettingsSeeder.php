<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\SubRegion;

class AreaSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 Starting Area Settings Seeder...\n";

        // Get languages
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        if ($languages->isEmpty()) {
            echo "❌ Error: No languages found. Please seed languages first.\n";
            return;
        }

        // Get countries
        $egypt = Country::where('code', 'eg')->first();
        $saudi = Country::where('code', 'sa')->first();

        if (!$egypt || !$saudi) {
            echo "❌ Error: Egypt (EG) or Saudi Arabia (SA) countries not found.\n";
            return;
        }

        echo "✓ Found languages: " . implode(', ', $languages->keys()->toArray()) . "\n";
        echo "✓ Found countries: Egypt (ID: {$egypt->id}), Saudi Arabia (ID: {$saudi->id})\n\n";

        // Update country translations
        echo "📍 Updating country translations...\n";
        $this->updateCountryTranslations($egypt, $languages, ['en' => 'Egypt', 'ar' => 'مصر']);
        $this->updateCountryTranslations($saudi, $languages, ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية']);

        // Seed Egypt data
        echo "\n📍 Seeding Egypt...\n";
        $this->seedEgypt($egypt, $languages);

        // Seed Saudi Arabia data
        echo "\n📍 Seeding Saudi Arabia...\n";
        $this->seedSaudiArabia($saudi, $languages);

        echo "\n🎉 Area Settings Seeder completed successfully!\n\n";
    }

    /**
     * Update country translations
     */
    private function updateCountryTranslations($country, $languages, $names)
    {
        foreach ($languages as $langCode => $language) {
            $country->translations()->updateOrCreate(
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                ],
                [
                    'lang_value' => $names[$langCode],
                ]
            );
        }
        echo "  ✓ Updated translations for: {$names['en']}\n";
    }

    /**
     * Seed Egypt cities, regions, and subregions
     */
    private function seedEgypt($country, $languages)
    {
        $egyptCities = [
            [
                'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
                'regions' => [
                    [
                        'name' => ['en' => 'Downtown Cairo', 'ar' => 'وسط البلد'],
                        'subregions' => [
                            ['name' => ['en' => 'Talaat Harb', 'ar' => 'طلعت حرب']],
                            ['name' => ['en' => 'Abdeen', 'ar' => 'عابدين']],
                            ['name' => ['en' => 'Bab El-Louk', 'ar' => 'باب اللوق']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
                        'subregions' => [
                            ['name' => ['en' => 'Dokki', 'ar' => 'الدقي']],
                            ['name' => ['en' => 'Agouza', 'ar' => 'العجوزة']],
                            ['name' => ['en' => 'Haram', 'ar' => 'الهرم']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Helwan', 'ar' => 'حلوان'],
                        'subregions' => [
                            ['name' => ['en' => 'Helwan City', 'ar' => 'مدينة حلوان']],
                            ['name' => ['en' => 'Ain Sokhna', 'ar' => 'عين السخنة']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
                'regions' => [
                    [
                        'name' => ['en' => 'Downtown Alexandria', 'ar' => 'وسط الإسكندرية'],
                        'subregions' => [
                            ['name' => ['en' => 'Raml Station', 'ar' => 'محطة الرمل']],
                            ['name' => ['en' => 'Saad Zaghloul', 'ar' => 'سعد زغلول']],
                            ['name' => ['en' => 'Anfushi', 'ar' => 'الأنفوشي']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Montaza', 'ar' => 'منتزة'],
                        'subregions' => [
                            ['name' => ['en' => 'Montaza Palace', 'ar' => 'قصر منتزة']],
                            ['name' => ['en' => 'Sidi Bishr', 'ar' => 'سيدي بشر']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
                'regions' => [
                    [
                        'name' => ['en' => 'Pyramids Area', 'ar' => 'منطقة الأهرام'],
                        'subregions' => [
                            ['name' => ['en' => 'Giza Plateau', 'ar' => 'هضبة الجيزة']],
                            ['name' => ['en' => 'Mohandessin', 'ar' => 'المهندسين']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Aswan', 'ar' => 'أسوان'],
                'regions' => [
                    [
                        'name' => ['en' => 'Aswan City', 'ar' => 'مدينة أسوان'],
                        'subregions' => [
                            ['name' => ['en' => 'Corniche', 'ar' => 'الكورنيش']],
                            ['name' => ['en' => 'Elephantine Island', 'ar' => 'جزيرة الفيلة']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Luxor', 'ar' => 'الأقصر'],
                'regions' => [
                    [
                        'name' => ['en' => 'Luxor City', 'ar' => 'مدينة الأقصر'],
                        'subregions' => [
                            ['name' => ['en' => 'East Bank', 'ar' => 'الضفة الشرقية']],
                            ['name' => ['en' => 'West Bank', 'ar' => 'الضفة الغربية']],
                        ]
                    ],
                ]
            ],
        ];

        $this->createCitiesWithRegionsAndSubregions($country, $languages, $egyptCities);
    }

    /**
     * Seed Saudi Arabia cities, regions, and subregions
     */
    private function seedSaudiArabia($country, $languages)
    {
        $saudiCities = [
            [
                'name' => ['en' => 'Riyadh', 'ar' => 'الرياض'],
                'regions' => [
                    [
                        'name' => ['en' => 'Al Olaya', 'ar' => 'العليا'],
                        'subregions' => [
                            ['name' => ['en' => 'Al Olaya District', 'ar' => 'حي العليا']],
                            ['name' => ['en' => 'Diplomatic Quarter', 'ar' => 'الحي الدبلوماسي']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Al Malaz', 'ar' => 'الملز'],
                        'subregions' => [
                            ['name' => ['en' => 'Al Malaz District', 'ar' => 'حي الملز']],
                            ['name' => ['en' => 'Al Nakheel', 'ar' => 'النخيل']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Al Batha', 'ar' => 'البطحاء'],
                        'subregions' => [
                            ['name' => ['en' => 'Al Batha District', 'ar' => 'حي البطحاء']],
                            ['name' => ['en' => 'Al Suwaidi', 'ar' => 'السويدي']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Jeddah', 'ar' => 'جدة'],
                'regions' => [
                    [
                        'name' => ['en' => 'Al Balad', 'ar' => 'البلد'],
                        'subregions' => [
                            ['name' => ['en' => 'Old Town', 'ar' => 'البلد القديم']],
                            ['name' => ['en' => 'Souk Al Alawi', 'ar' => 'سوق العلوي']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Al Nuzha', 'ar' => 'النزهة'],
                        'subregions' => [
                            ['name' => ['en' => 'Al Nuzha District', 'ar' => 'حي النزهة']],
                            ['name' => ['en' => 'Al Rawda', 'ar' => 'الروضة']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Corniche', 'ar' => 'الكورنيش'],
                        'subregions' => [
                            ['name' => ['en' => 'North Corniche', 'ar' => 'الكورنيش الشمالي']],
                            ['name' => ['en' => 'South Corniche', 'ar' => 'الكورنيش الجنوبي']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Mecca', 'ar' => 'مكة'],
                'regions' => [
                    [
                        'name' => ['en' => 'Al Haram', 'ar' => 'الحرم'],
                        'subregions' => [
                            ['name' => ['en' => 'Holy Mosque Area', 'ar' => 'منطقة المسجد الحرام']],
                            ['name' => ['en' => 'Ajyad', 'ar' => 'أجياد']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Medina', 'ar' => 'المدينة'],
                'regions' => [
                    [
                        'name' => ['en' => 'Al Masjid Al Nabawi', 'ar' => 'المسجد النبوي'],
                        'subregions' => [
                            ['name' => ['en' => 'Prophet Mosque Area', 'ar' => 'منطقة المسجد النبوي']],
                            ['name' => ['en' => 'Al Manakha', 'ar' => 'المناخة']],
                        ]
                    ],
                ]
            ],
            [
                'name' => ['en' => 'Dammam', 'ar' => 'الدمام'],
                'regions' => [
                    [
                        'name' => ['en' => 'Al Khobar', 'ar' => 'الخبر'],
                        'subregions' => [
                            ['name' => ['en' => 'Corniche', 'ar' => 'الكورنيش']],
                            ['name' => ['en' => 'Downtown', 'ar' => 'وسط المدينة']],
                        ]
                    ],
                    [
                        'name' => ['en' => 'Dhahran', 'ar' => 'الظهران'],
                        'subregions' => [
                            ['name' => ['en' => 'Aramco Area', 'ar' => 'منطقة أرامكو']],
                        ]
                    ],
                ]
            ],
        ];

        $this->createCitiesWithRegionsAndSubregions($country, $languages, $saudiCities);
    }

    /**
     * Create cities with regions and subregions
     */
    private function createCitiesWithRegionsAndSubregions($country, $languages, $citiesData)
    {
        foreach ($citiesData as $cityData) {
            try {
                // Create city
                $city = City::create([
                    'country_id' => $country->id,
                    'active' => 1,
                ]);

                // Add city translations
                foreach ($languages as $langCode => $language) {
                    $city->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $cityData['name'][$langCode],
                    ]);
                }

                echo "  ✓ Created city: {$cityData['name']['en']}\n";

                // Create regions for this city
                foreach ($cityData['regions'] as $regionData) {
                    try {
                        $region = Region::create([
                            'city_id' => $city->id,
                            'active' => 1,
                        ]);

                        // Add region translations
                        foreach ($languages as $langCode => $language) {
                            $region->translations()->create([
                                'lang_id' => $language->id,
                                'lang_key' => 'name',
                                'lang_value' => $regionData['name'][$langCode],
                            ]);
                        }

                        echo "    ✓ Created region: {$regionData['name']['en']}\n";

                        // Create subregions for this region
                        foreach ($regionData['subregions'] as $subregionData) {
                            try {
                                $subregion = SubRegion::create([
                                    'region_id' => $region->id,
                                    'active' => 1,
                                ]);

                                // Add subregion translations
                                foreach ($languages as $langCode => $language) {
                                    $subregion->translations()->create([
                                        'lang_id' => $language->id,
                                        'lang_key' => 'name',
                                        'lang_value' => $subregionData['name'][$langCode],
                                    ]);
                                }

                                echo "      ✓ Created subregion: {$subregionData['name']['en']}\n";
                            } catch (\Exception $e) {
                                echo "      ✗ Failed to create subregion {$subregionData['name']['en']}: {$e->getMessage()}\n";
                            }
                        }
                    } catch (\Exception $e) {
                        echo "    ✗ Failed to create region {$regionData['name']['en']}: {$e->getMessage()}\n";
                    }
                }
            } catch (\Exception $e) {
                echo "  ✗ Failed to create city {$cityData['name']['en']}: {$e->getMessage()}\n";
            }
        }
    }
}
