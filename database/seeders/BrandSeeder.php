<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Language;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\AreaSettings\app\Models\Country;

class BrandSeeder extends Seeder
{
    /**
     * Brand data with translations
     */
    private $brandsData = [
        // Electronics
        ['en' => 'Apple', 'ar' => 'أبل'],
        ['en' => 'Samsung', 'ar' => 'سامسونج'],
        ['en' => 'Sony', 'ar' => 'سوني'],
        ['en' => 'LG', 'ar' => 'إل جي'],
        ['en' => 'Dell', 'ar' => 'ديل'],
        ['en' => 'HP', 'ar' => 'إتش بي'],
        ['en' => 'Lenovo', 'ar' => 'لينوفو'],
        ['en' => 'ASUS', 'ar' => 'أسوس'],

        // Fashion
        ['en' => 'Nike', 'ar' => 'نايك'],
        ['en' => 'Adidas', 'ar' => 'أديداس'],
        ['en' => 'Puma', 'ar' => 'بوما'],
        ['en' => 'Gucci', 'ar' => 'جوتشي'],
        ['en' => 'Louis Vuitton', 'ar' => 'لويس فويتون'],
        ['en' => 'Zara', 'ar' => 'زارا'],
        ['en' => 'H&M', 'ar' => 'إتش أند إم'],

        // Home & Garden
        ['en' => 'IKEA', 'ar' => 'إيكيا'],
        ['en' => 'Philips', 'ar' => 'فيليبس'],
        ['en' => 'Bosch', 'ar' => 'بوش'],
        ['en' => 'Siemens', 'ar' => 'سيمنس'],

        // Beauty
        ['en' => 'L\'Oreal', 'ar' => 'لوريال'],
        ['en' => 'Estee Lauder', 'ar' => 'إستي لودر'],
        ['en' => 'Maybelline', 'ar' => 'ميبيلين'],
        ['en' => 'Dove', 'ar' => 'دوف'],

        // Sports
        ['en' => 'Decathlon', 'ar' => 'ديكاثلون'],
        ['en' => 'Spalding', 'ar' => 'سبولدينج'],
        ['en' => 'Wilson', 'ar' => 'ويلسون'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🏷️  Starting Brand Seeder...\n";

        // Get languages
        $languages = Language::all()->keyBy('code');
        if ($languages->isEmpty()) {
            echo "❌ No languages found. Please run LanguageSeeder first.\n";
            return;
        }

        // Get country from session
        $countryCode = session('country_code', 'EG');
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = Country::first();
        }

        if (!$country) {
            echo "❌ No countries found. Please create a country first.\n";
            return;
        }

        $countryId = $country->id;

        // Create brands
        foreach ($this->brandsData as $brandData) {
            $this->createBrand($brandData, $languages, $countryId);
        }

        echo "\n✅ Brand Seeder completed!\n";
    }

    /**
     * Create a brand with translations
     */
    private function createBrand($brandData, $languages, $countryId)
    {
        // Generate base slug
        $baseSlug = Str::slug($brandData['en']);


        // Generate unique slug globally (in case of race conditions)
        $slug = $baseSlug;
        $counter = 1;
        while (Brand::where('slug', $slug)
            ->withoutCountryFilter()->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        try {
            // Create brand
            $brand = Brand::create([
                'slug' => $slug,
                'active' => true,
                'country_id' => $countryId,
            ]);

            // Add translations
            foreach ($languages as $langCode => $language) {
                $brand->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $langCode === 'en' ? $brandData['en'] : $brandData['ar'],
                ]);
            }

            echo "  ✓ Created brand: {$brandData['en']} (Country ID: {$countryId})\n";
        } catch (\Exception $e) {
            echo "  ⏭️ Skipped brand: {$brandData['en']} (error: {$e->getMessage()})\n";
        }
    }
}
