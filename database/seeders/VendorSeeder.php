<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Language;
use App\Models\User;
use App\Models\UserType;
use Modules\Vendor\app\Models\Vendor;
use Modules\AreaSettings\app\Models\Country;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CatalogManagement\app\Models\Brand;

class VendorSeeder extends Seeder
{
    /**
     * Vendor data with translations
     */
    private $vendorsData = [
        [
            'en' => 'TechHub Store',
            'ar' => 'متجر تك هاب',
        ],
        [
            'en' => 'Fashion Plus',
            'ar' => 'فاشن بلس',
        ],
        [
            'en' => 'Home Comfort',
            'ar' => 'راحة المنزل',
        ],
        [
            'en' => 'Beauty World',
            'ar' => 'عالم الجمال',
        ],
        [
            'en' => 'Sports Pro',
            'ar' => 'سبورتس برو',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "\n🏪 Starting Vendor Seeder...\n";

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

        // Get departments for this country
        $departments = Department::where('country_id', $countryId)->get();
        if ($departments->isEmpty()) {
            echo "❌ No departments found for country {$country->code}. Please run CategoryDepartmentSeeder first.\n";
            return;
        }

        // Create vendors
        foreach ($this->vendorsData as $vendorData) {
            $this->createVendor($vendorData, $languages, $countryId, $departments);
        }

        echo "\n✅ Vendor Seeder completed!\n";
    }

    /**
     * Create a vendor with translations
     */
    private function createVendor($vendorData, $languages, $countryId, $departments)
    {
        // ... (slug logic unchanged) ...
        $baseSlug = Str::slug($vendorData['en']);

        // Generate unique slug (globally unique across all countries)
        $slug = $baseSlug;
        $counter = 1;
        while (Vendor::where('slug', $slug)->withoutCountryFilter()->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Generate unique email
        $baseEmail = $baseSlug . '@vendor.test';
        $email = $baseEmail;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $emailParts = explode('@', $baseEmail);
            $email = $emailParts[0] . '-' . $counter . '@' . $emailParts[1];
            $counter++;
        }

        try {
            // Create vendor user
            $vendorUser = User::create([
                'country_id' => $countryId,
                'uuid' => Str::uuid(),
                'email' => $email,
                'password' => bcrypt('password'),
                'user_type_id' => UserType::where('name', 'vendor')->first()->id ?? 3,
                'active' => 1,
            ]);

            // Get random department from country
            $department = $departments->where('country_id', $countryId)->random();

            // Get random brand from country
            $brand = Brand::where('country_id', $countryId)->inRandomOrder()->first();

            // Create vendor
            $vendor = Vendor::create([
                'user_id' => $vendorUser->id,
                'country_id' => $countryId,
                'slug' => $slug,
                'type' => 'product',
                'active' => 1,
            ]);

            // Add translations
            foreach ($languages as $langCode => $language) {
                $vendor->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $langCode === 'en' ? $vendorData['en'] : $vendorData['ar'],
                ]);
            }

            // Attach random department
            if ($department) {
                $vendor->departments()->attach($department->id);
            }

            $brandName = $brand ? $brand->getTranslation('name', 'en') : 'None';
            echo "  ✓ Created vendor: {$vendorData['en']} (Department: {$department?->name}, Brand: {$brandName}, Country ID: {$countryId})\n";
        } catch (\Exception $e) {
            echo "  ⏭️ Skipped vendor: {$vendorData['en']} (error: {$e->getMessage()})\n";
        }
    }
}
