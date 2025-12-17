<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Language;
use Modules\Vendor\app\Models\Vendor;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Country;

class AutoProductSeeder extends Seeder
{
    private $faker;
    private $fakerAr;

    private $productCategories = [
        'Electronics' => ['Smartphone', 'Laptop', 'Tablet', 'Headphones', 'Speaker', 'Camera', 'Monitor', 'Keyboard', 'Mouse', 'Smartwatch'],
        'Fashion' => ['T-Shirt', 'Jeans', 'Dress', 'Jacket', 'Sneakers', 'Boots', 'Hat', 'Scarf', 'Sunglasses', 'Belt'],
        'Home' => ['Sofa', 'Table', 'Chair', 'Lamp', 'Rug', 'Curtain', 'Pillow', 'Vase', 'Mirror', 'Clock'],
        'Sports' => ['Running Shoes', 'Yoga Mat', 'Dumbbell', 'Treadmill', 'Bicycle', 'Tennis Racket', 'Football', 'Basketball', 'Swim Goggles', 'Gym Bag'],
        'Beauty' => ['Lipstick', 'Foundation', 'Mascara', 'Perfume', 'Face Cream', 'Shampoo', 'Body Lotion', 'Nail Polish', 'Eye Shadow', 'Brush Set'],
        'Books' => ['Novel', 'Textbook', 'Cookbook', 'Comic Book', 'Magazine', 'Dictionary', 'Biography', 'Poetry Book', 'Travel Guide', 'Art Book'],
        'Toys' => ['Action Figure', 'Doll', 'Puzzle', 'Board Game', 'LEGO Set', 'RC Car', 'Stuffed Animal', 'Building Blocks', 'Educational Toy', 'Art Supplies'],
        'Kitchen' => ['Blender', 'Coffee Maker', 'Toaster', 'Microwave', 'Knife Set', 'Cutting Board', 'Pan', 'Pot', 'Dish Set', 'Utensil Set'],
    ];

    private $adjectives = [
        'Premium', 'Professional', 'Deluxe', 'Ultra', 'Pro', 'Smart', 'Classic', 'Modern', 'Vintage', 'Luxury',
        'Elite', 'Advanced', 'Supreme', 'Ultimate', 'Enhanced', 'Optimized', 'Refined', 'Superior', 'Exclusive', 'Special'
    ];

    public function __construct()
    {
        $this->faker = Faker::create('en_US');
        $this->fakerAr = Faker::create('ar_SA');
    }

    public function run(): void
    {
        echo "\n🚀 Starting Auto Product Seeder...\n";

        // Get country_id from session
        $countryCode = session('country_code', 'EG');
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = Country::first();
        }

        $countryId = $country ? $country->id : null;

        if (!$countryId) {
            echo "❌ Error: No country found for code: {$countryCode}\n";
            return;
        }

        echo "✓ Using country: {$country->code} (ID: {$countryId})\n";

        // Fetch required data
        $vendors = Vendor::where('active', 1)->where('country_id', $countryId)->get();
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');
        $brands = Brand::where('active', 1)->where('country_id', $countryId)->get();
        $taxes = Tax::where('active', 1)->where('country_id', $countryId)->get();

        // Get regions through cities for this country
        $regions = Region::whereHas('city', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->where('active', 1)->get();

        $variantKeys = VariantConfigurationKey::where('country_id', $countryId)->get();

        // Debug: Check all vendors
        $allVendors = Vendor::all();
        echo "📊 Total vendors in DB: {$allVendors->count()}\n";
        foreach ($allVendors as $v) {
            echo "  - Vendor ID: {$v->id}, Active: {$v->active}, Country ID: {$v->country_id}\n";
        }

        if ($vendors->isEmpty()) {
            echo "❌ Error: No active vendors found for country: {$country->code}\n";
            echo "   Searched for: active=1, country_id={$countryId}\n";
            return;
        }

        if ($brands->isEmpty()) {
            echo "❌ Error: Missing brands for country {$countryCode}\n";
            return;
        }

        if ($regions->isEmpty()) {
            echo "❌ Error: No regions found for country {$countryCode}\n";
            return;
        }

        echo "✓ Found {$vendors->count()} active vendors\n";
        echo "✓ Found {$brands->count()} brands\n";
        echo "✓ Found {$regions->count()} regions\n";
        echo "✓ Found {$variantKeys->count()} variant keys\n";

        $totalProducts = 0;

        foreach ($vendors as $vendor) {
            echo "📦 Creating products for vendor: {$vendor->getTranslation('name', 'en')}\n";

            // Get departments, categories, and sub-categories related to this vendor's activities
            // Get departments related to this vendor directly
            $vendorDepartments = $vendor->departments()
                ->where('active', 1)
                ->where('country_id', $countryId)
                ->get();

            if ($vendorDepartments->isEmpty()) {
                echo "  ⚠️  No departments found for vendor activities. Skipping...\n";
                continue;
            }

            // Get categories related to vendor's departments
            $vendorCategories = Category::where('active', 1)
                ->where('country_id', $countryId)
                ->whereIn('department_id', $vendorDepartments->pluck('id'))
                ->get();

            // Get sub-categories related to vendor's categories
            $vendorSubCategories = SubCategory::where('active', 1)
                ->where('country_id', $countryId)
                ->whereIn('category_id', $vendorCategories->pluck('id'))
                ->get();

            if ($vendorCategories->isEmpty()) {
                echo "  ⚠️  No categories found for vendor departments. Skipping...\n";
                continue;
            }

            for ($i = 1; $i <= 40; $i++) {
                try {
                    // 60% simple, 40% variant
                    $isVariant = $this->faker->boolean(40);
                    if ($isVariant && !$variantKeys->isEmpty()) {
                        $this->createVariantProduct($vendor, $languages, $brands, $vendorDepartments, $vendorCategories, $vendorSubCategories, $taxes, $regions, $variantKeys, $countryId);
                    } else {
                        $this->createSimpleProduct($vendor, $languages, $brands, $vendorDepartments, $vendorCategories, $vendorSubCategories, $taxes, $regions, $countryId);
                    }
                    $totalProducts++;
                } catch (\Exception $e) {
                    echo "  ❌ Error creating product {$i}: {$e->getMessage()}\n";
                }
            }

            echo "✅ Completed {$vendor->getTranslation('name', 'en')}: 40 products created\n\n";
        }

        echo "\n🎉 Auto Product Seeder completed successfully!\n";
        echo "📊 Total products created: {$totalProducts}\n";
        echo "📊 Total vendors processed: {$vendors->count()}\n\n";
    }

    private function createSimpleProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions, $countryId)
    {
        try {
            $productName = $this->generateProductName();

            // Check if collections are empty before using random()
            if ($categories->isEmpty()) {
                throw new \Exception('Categories collection is empty');
            }
            $category = $categories->random();

            if ($brands->isEmpty()) {
                throw new \Exception('Brands collection is empty');
            }

            if ($departments->isEmpty()) {
                throw new \Exception('Departments collection is empty');
            }

            if ($taxes->isEmpty()) {
                throw new \Exception('Taxes collection is empty');
            }

            $subCategoryId = null;
            if ($subCategories->isNotEmpty()) {
                $categorySubCategories = $subCategories->where('category_id', $category->id);
                if ($categorySubCategories->isNotEmpty()) {
                    $subCategoryId = $categorySubCategories->random()->id;
                }
            }

            $product = Product::create([
                'slug' => Str::slug($productName) . '-' . Str::random(6),
                'type' => Product::TYPE_PRODUCT,
                'configuration_type' => 'simple',
                'is_active' => $this->faker->boolean(90),
                'brand_id' => $brands->random()->id,
                'department_id' => $departments->random()->id,
                'category_id' => $category->id,
                'sub_category_id' => $subCategoryId,
                'vendor_id' => $vendor->id,
                'created_by_user_id' => $vendor->user_id,
                'country_id' => $countryId,
            ]);

        foreach ($languages as $langCode => $language) {
            $product->translations()->createMany([
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'title',
                    'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'details',
                    'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(3, true) : $this->fakerAr->paragraphs(3, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'summary',
                    'lang_value' => $langCode === 'en' ? $this->faker->sentence(15) : $this->fakerAr->sentence(15),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'features',
                    'lang_value' => $langCode === 'en' ? $this->generateFeatures() : $this->generateFeaturesAr(),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'instructions',
                    'lang_value' => $langCode === 'en' ? $this->generateInstructions() : $this->generateInstructionsAr(),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'extra_description',
                    'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(2, true) : $this->fakerAr->paragraphs(2, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'material',
                    'lang_value' => $langCode === 'en' ? $this->faker->randomElement(['Cotton', 'Polyester', 'Leather', 'Plastic', 'Metal', 'Wood', 'Glass', 'Ceramic', 'Rubber', 'Silk']) : $this->fakerAr->randomElement(['قطن', 'بوليستر', 'جلد', 'بلاستيك', 'معادن', 'خشب', 'زجاج', 'سيراميك', 'مطاط', 'حرير']),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'video_link',
                    'lang_value' => 'https://www.youtube.com/watch?v=' . $this->faker->bothify('??????????'),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_title',
                    'lang_value' => $langCode === 'en' ? $productName . ' - Premium Quality' : $this->fakerAr->words(4, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_description',
                    'lang_value' => $langCode === 'en' ? $this->faker->sentence(20) : $this->fakerAr->sentence(20),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_keywords',
                    'lang_value' => json_encode($langCode === 'en' ? $this->faker->words(5) : $this->fakerAr->words(5)),
                ],
            ]);
        }

        $vendorProduct = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_active' => $product->is_active,
            'status' => $this->faker->randomElement(['pending', 'approved']),
            'tax_id' => $taxes->random()->id,
            'sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'max_per_order' => $this->faker->numberBetween(1, 10),
            'is_featured' => $this->faker->boolean(20),
            'country_id' => $countryId,
        ]);


        $vendorProductVariant = VendorProductVariant::create([
            'vendor_product_id' => $vendorProduct->id,
            'variant_configuration_id' => null,
            'sku' => $vendorProduct->sku,
            'price' => $this->faker->randomFloat(2, 50, 5000),
            'price_before_discount' => 0,
            'has_discount' => false,
        ]);

        if ($regions->isNotEmpty()) {
            $numRegions = min(5, $regions->count());
            foreach ($regions->random($this->faker->numberBetween(1, $numRegions)) as $region) {
                VendorProductVariantStock::create([
                    'vendor_product_variant_id' => $vendorProductVariant->id,
                    'region_id' => $region->id,
                    'quantity' => $this->faker->numberBetween(5, 200),
                ]);
            }
        }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function createVariantProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions, $variantKeys, $countryId)
    {
        try {
            $productName = $this->generateProductName();

            // Check if collections are empty before using random()
            if ($categories->isEmpty()) {
                throw new \Exception('Categories collection is empty');
            }
            $category = $categories->random();

            if ($brands->isEmpty()) {
                throw new \Exception('Brands collection is empty');
            }

            if ($departments->isEmpty()) {
                throw new \Exception('Departments collection is empty');
            }

            if ($taxes->isEmpty()) {
                throw new \Exception('Taxes collection is empty');
            }

            $subCategoryId = null;
            if ($subCategories->isNotEmpty()) {
                $categorySubCategories = $subCategories->where('category_id', $category->id);
                if ($categorySubCategories->isNotEmpty()) {
                    $subCategoryId = $categorySubCategories->random()->id;
                }
            }

            $product = Product::create([
                'slug' => Str::slug($productName) . '-' . Str::random(6),
                'type' => Product::TYPE_PRODUCT,
                'configuration_type' => 'variants',
                'is_active' => $this->faker->boolean(90),
                'brand_id' => $brands->random()->id,
                'department_id' => $departments->random()->id,
                'category_id' => $category->id,
                'sub_category_id' => $subCategoryId,
                'vendor_id' => $vendor->id,
                'created_by_user_id' => $vendor->user_id,
                'country_id' => $countryId,
            ]);

        foreach ($languages as $langCode => $language) {
            $product->translations()->createMany([
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'title',
                    'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'details',
                    'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(3, true) : $this->fakerAr->paragraphs(3, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'summary',
                    'lang_value' => $langCode === 'en' ? $this->faker->sentence(15) : $this->fakerAr->sentence(15),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'features',
                    'lang_value' => $langCode === 'en' ? $this->generateFeatures() : $this->generateFeaturesAr(),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'instructions',
                    'lang_value' => $langCode === 'en' ? $this->generateInstructions() : $this->generateInstructionsAr(),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'extra_description',
                    'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(2, true) : $this->fakerAr->paragraphs(2, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'material',
                    'lang_value' => $langCode === 'en' ? $this->faker->randomElement(['Cotton', 'Polyester', 'Leather', 'Plastic', 'Metal', 'Wood', 'Glass', 'Ceramic', 'Rubber', 'Silk']) : $this->fakerAr->randomElement(['قطن', 'بوليستر', 'جلد', 'بلاستيك', 'معادن', 'خشب', 'زجاج', 'سيراميك', 'مطاط', 'حرير']),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'video_link',
                    'lang_value' => 'https://www.youtube.com/watch?v=' . $this->faker->bothify('??????????'),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_title',
                    'lang_value' => $langCode === 'en' ? $productName . ' - Premium Quality' : $this->fakerAr->words(4, true),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_description',
                    'lang_value' => $langCode === 'en' ? $this->faker->sentence(20) : $this->fakerAr->sentence(20),
                ],
                [
                    'lang_id' => $language->id,
                    'lang_key' => 'meta_keywords',
                    'lang_value' => json_encode($langCode === 'en' ? $this->faker->words(5) : $this->fakerAr->words(5)),
                ],
            ]);
        }

        $vendorProduct = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_active' => $product->is_active,
            'status' => $this->faker->randomElement(['pending', 'approved']),
            'tax_id' => $taxes->random()->id,
            'sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'max_per_order' => $this->faker->numberBetween(1, 10),
            'is_featured' => $this->faker->boolean(20),
            'country_id' => $countryId,
        ]);

        // Get variant keys for this country
        if ($variantKeys->isEmpty()) {
            // fallback to simple variant
            $this->createSimpleProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions, $countryId);
            return;
        }

        // Select 1-3 random variant keys
        $selectedKeys = $variantKeys->random($this->faker->numberBetween(1, min(3, $variantKeys->count())));

        foreach ($selectedKeys as $variantKey) {
            // Get variant values for this key
            $variantValues = VariantsConfiguration::where('key_id', $variantKey->id)->get();

            if ($variantValues->isEmpty()) {
                continue;
            }

            // Select a random value for this key
            $selectedValue = $variantValues->random();
            $variantTitle = $selectedValue->getTranslation('name', 'en') ?? 'Variant';
            $price = $this->faker->randomFloat(2, 50, 5000);
            $hasDiscount = $this->faker->boolean(30);

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'variant_configuration_id' => $selectedValue->id,
            ]);

            foreach ($languages as $langCode => $language) {
                $translatedTitle = $selectedValue->getTranslation('name', $langCode) ?? $variantTitle;
                $variant->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'title',
                    'lang_value' => $translatedTitle,
                ]);
            }

            $vendorProductVariant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProduct->id,
                'variant_configuration_id' => $selectedValue->id,
                'sku' => $vendorProduct->sku . '-' . strtoupper(substr($variantTitle, 0, 3)),
                'price' => $price,
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? $price * $this->faker->randomFloat(2, 1.15, 1.5) : 0,
                'discount_end_date' => $hasDiscount ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            ]);

            if ($regions->isNotEmpty()) {
                $numRegions = min(5, $regions->count());
                foreach ($regions->random($this->faker->numberBetween(1, $numRegions)) as $region) {
                    VendorProductVariantStock::create([
                        'vendor_product_variant_id' => $vendorProductVariant->id,
                        'region_id' => $region->id,
                        'quantity' => $this->faker->numberBetween(5, 200),
                    ]);
                }
            }
        }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function generateProductName(): string
    {
        $category = $this->faker->randomElement(array_keys($this->productCategories));
        $productType = $this->faker->randomElement($this->productCategories[$category]);
        $adjective = $this->faker->randomElement($this->adjectives);
        return "$adjective $productType";
    }

    private function generateFeatures(): string
    {
        $features = [];
        for ($i = 0; $i < $this->faker->numberBetween(4, 8); $i++) {
            $features[] = "• " . $this->faker->sentence($this->faker->numberBetween(4, 8));
        }
        return implode("\n", $features);
    }

    private function generateFeaturesAr(): string
    {
        $features = [];
        for ($i = 0; $i < $this->faker->numberBetween(4, 8); $i++) {
            $features[] = "• " . $this->fakerAr->sentence($this->faker->numberBetween(4, 8));
        }
        return implode("\n", $features);
    }

    private function generateInstructions(): string
    {
        $instructions = [];
        $steps = $this->faker->numberBetween(3, 6);
        for ($i = 1; $i <= $steps; $i++) {
            $instructions[] = "Step $i: " . $this->faker->sentence($this->faker->numberBetween(5, 10));
        }
        return implode("\n", $instructions);
    }

    private function generateInstructionsAr(): string
    {
        $instructions = [];
        $steps = $this->faker->numberBetween(3, 6);
        for ($i = 1; $i <= $steps; $i++) {
            $instructions[] = "الخطوة $i: " . $this->fakerAr->sentence($this->faker->numberBetween(5, 10));
        }
        return implode("\n", $instructions);
    }
}
