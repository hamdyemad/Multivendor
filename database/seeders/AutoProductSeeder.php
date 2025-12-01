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

class AutoProductSeeder extends Seeder
{
    private $faker;
    private $fakerAr;

    /**
     * Product categories for realistic naming
     */
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

    private $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Purple', 'Orange', 'Pink', 'Gray', 'Brown', 'Navy', 'Beige'];
    private $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    private $numericalSizes = ['38', '39', '40', '41', '42', '43', '44', '45'];

    public function __construct()
    {
        $this->faker = Faker::create('en_US');
        $this->fakerAr = Faker::create('ar_SA');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 Starting Auto Product Seeder...\n";

        // Get required data
        $vendors = Vendor::where('active', true)->get();
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');
        $brands = Brand::where('active', true)->get();
        $departments = Department::where('active', true)->get();
        $categories = Category::where('active', true)->get();
        $subCategories = SubCategory::where('active', true)->get();
        $taxes = Tax::where('active', true)->get();
        $regions = Region::where('active', true)->get();

        // Setup variant configuration keys and values
        $this->setupVariantConfigurations($languages);

        // Validation
        if ($vendors->isEmpty()) {
            echo "❌ Error: No active vendors found. Please create vendors first.\n";
            return;
        }

        if ($brands->isEmpty() || $departments->isEmpty() || $categories->isEmpty() ||
            $subCategories->isEmpty() || $taxes->isEmpty() || $regions->isEmpty()) {
            echo "❌ Error: Missing required data (brands, departments, categories, taxes, or regions).\n";
            return;
        }

        echo "✓ Found {$vendors->count()} active vendors\n";
        echo "✓ Found {$brands->count()} brands, {$departments->count()} departments, {$categories->count()} categories\n";
        echo "✓ Found {$regions->count()} regions for stock management\n\n";

        $totalProducts = 0;

        foreach ($vendors as $vendor) {
            echo "📦 Creating products for vendor: {$vendor->getTranslation('name', 'en')}\n";

            for ($i = 1; $i <= 40; $i++) {
                // Randomize product type (60% simple, 40% variants)
                $isVariant = $this->faker->boolean(40);

                try {
                    if ($isVariant) {
                        $this->createVariantProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions);
                        echo "  ✓ Created variant product {$i}/40\n";
                    } else {
                        $this->createSimpleProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions);
                        echo "  ✓ Created simple product {$i}/40\n";
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

    /**
     * Create a simple product
     */
    private function createSimpleProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions)
    {
        $productName = $this->generateProductName();

        // Get a random category and optionally a subcategory from categories table
        $category = $categories->random();
        $subCategory = Category::where('parent_id', $category->id)->where('active', true)->inRandomOrder()->first();

        // Create base product
        $product = Product::create([
            'slug' => Str::slug($productName) . '-' . Str::random(6),
            'type' => Product::TYPE_PRODUCT,
            'configuration_type' => 'simple',
            'is_active' => $this->faker->boolean(90),
            'brand_id' => $brands->random()->id,
            'department_id' => $departments->random()->id,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory ? $subCategory->id : null,
            'vendor_id' => $vendor->id,
            'created_by_user_id' => $vendor->user_id,
        ]);

        // Store translations
        foreach ($languages as $langCode => $language) {
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'title',
                'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'details',
                'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(3, true) : $this->fakerAr->paragraphs(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'summary',
                'lang_value' => $langCode === 'en' ? $this->faker->sentence(15) : $this->fakerAr->sentence(15),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'features',
                'lang_value' => $langCode === 'en' ? $this->generateFeatures() : $this->generateFeaturesAr(),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'instructions',
                'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(2, true) : $this->fakerAr->paragraphs(2, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_title',
                'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_description',
                'lang_value' => $langCode === 'en' ? $this->faker->sentence(20) : $this->fakerAr->sentence(20),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_keywords',
                'lang_value' => $langCode === 'en' ? implode(', ', $this->faker->words(8)) : implode(', ', $this->fakerAr->words(8)),
            ]);
        }

        // Create vendor product
        $price = $this->faker->randomFloat(2, 50, 5000);
        $hasDiscount = $this->faker->boolean(30);

        $vendorProduct = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_active' => $product->is_active,
            'status' => $this->faker->randomElement(['pending', 'approved', 'approved', 'approved']),
            'tax_id' => $taxes->random()->id,
            'sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'max_per_order' => $this->faker->numberBetween(1, 10),
            'is_featured' => $this->faker->boolean(20),
        ]);

        // Get any available variant configuration for simple products
        $standardConfig = null;
        $variantKeys = VariantConfigurationKey::all();

        foreach ($variantKeys as $key) {
            $configs = VariantsConfiguration::where('key_id', $key->id)->first();
            if ($configs) {
                $standardConfig = $configs;
                break;
            }
        }

        // If no configurations found, create a default one
        if (!$standardConfig && !$variantKeys->isEmpty()) {
            $firstKey = $variantKeys->first();
            $standardConfig = VariantsConfiguration::create([
                'key_id' => $firstKey->id,
                'parent_id' => null,
            ]);

            foreach ($languages as $langCode => $language) {
                $standardConfig->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $langCode === 'en' ? 'Standard' : 'قياسي',
                ]);
            }
        }

        // Create single product variant (for simple products)
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'variant_configuration_id' => $standardConfig->id,
        ]);

        // Store variant translation
        foreach ($languages as $langCode => $language) {
            $variant->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'title',
                'lang_value' => $langCode === 'en' ? 'Standard' : 'قياسي',
            ]);
        }

        // Create vendor product variant with pricing
        $vendorProductVariant = VendorProductVariant::create([
            'vendor_product_id' => $vendorProduct->id,
            'variant_configuration_id' => $standardConfig->id,
            'sku' => $vendorProduct->sku,
            'price' => $price,
            'has_discount' => $hasDiscount,
            'price_before_discount' => $hasDiscount ? $price * $this->faker->randomFloat(2, 1.15, 1.5) : 0,
            'discount_end_date' => $hasDiscount ? $this->faker->dateTimeBetween('now', '+3 months') : null,
        ]);

        // Create regional stock
        foreach ($regions->random($this->faker->numberBetween(2, min(5, $regions->count()))) as $region) {
            VendorProductVariantStock::create([
                'vendor_product_variant_id' => $vendorProductVariant->id,
                'region_id' => $region->id,
                'quantity' => $this->faker->numberBetween(10, 500),
            ]);
        }
    }

    /**
     * Setup variant configuration keys and values
     */
    private function setupVariantConfigurations($languages)
    {
        // Get all existing variant configuration keys
        $variantKeys = VariantConfigurationKey::all();

        // If no keys exist, create default Color and Size keys
        if ($variantKeys->isEmpty()) {
            $this->createDefaultVariantKeys($languages);
            $variantKeys = VariantConfigurationKey::all();
        }

        // Loop through each variant key and setup its configurations
        foreach ($variantKeys as $key) {
            $keyName = $key->getTranslation('name', 'en');

            // Get existing configurations for this key
            $existingConfigs = VariantsConfiguration::where('key_id', $key->id)->get();

            echo "✓ Found variant key: {$keyName} with {$existingConfigs->count()} configurations\n";

            // Setup configurations based on key type
            if (strtolower($keyName) === 'color') {
                $this->setupColorConfigurations($key, $languages, $existingConfigs);
            } elseif (strtolower($keyName) === 'size') {
                $this->setupSizeConfigurations($key, $languages, $existingConfigs);
            }
        }
    }

    /**
     * Create default variant keys if none exist
     */
    private function createDefaultVariantKeys($languages)
    {
        // Create Color key
        $colorKey = VariantConfigurationKey::create(['id' => 1]);
        foreach ($languages as $langCode => $language) {
            $colorKey->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'name',
                'lang_value' => $langCode === 'en' ? 'Color' : 'اللون',
            ]);
        }

        // Create Size key
        $sizeKey = VariantConfigurationKey::create(['id' => 2]);
        foreach ($languages as $langCode => $language) {
            $sizeKey->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'name',
                'lang_value' => $langCode === 'en' ? 'Size' : 'الحجم',
            ]);
        }
    }

    /**
     * Setup color configurations for a key
     */
    private function setupColorConfigurations($key, $languages, $existingConfigs)
    {
        foreach ($this->colors as $color) {
            $existingConfig = $existingConfigs->filter(function($config) use ($color) {
                return $config->getTranslation('name', 'en') === $color;
            })->first();

            if (!$existingConfig) {
                $config = VariantsConfiguration::create([
                    'key_id' => $key->id,
                    'parent_id' => null,
                ]);

                foreach ($languages as $langCode => $language) {
                    $config->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $color : $this->translateColorToArabic($color),
                    ]);
                }
            }
        }
    }

    /**
     * Setup size configurations for a key
     */
    private function setupSizeConfigurations($key, $languages, $existingConfigs)
    {
        $allSizes = array_merge($this->sizes, $this->numericalSizes);

        foreach ($allSizes as $size) {
            $existingConfig = $existingConfigs->filter(function($config) use ($size) {
                return $config->getTranslation('name', 'en') === $size;
            })->first();

            if (!$existingConfig) {
                $config = VariantsConfiguration::create([
                    'key_id' => $key->id,
                    'parent_id' => null,
                ]);

                foreach ($languages as $langCode => $language) {
                    $config->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $size,
                    ]);
                }
            }
        }

        echo "✓ Setup variant configurations completed\n";
    }

    /**
     * Create a variant product
     */
    private function createVariantProduct($vendor, $languages, $brands, $departments, $categories, $subCategories, $taxes, $regions)
    {
        $productName = $this->generateProductName();

        // Get a random category and optionally a subcategory from categories table
        $category = $categories->random();
        $subCategory = Category::where('parent_id', $category->id)->where('active', true)->inRandomOrder()->first();

        // Create base product
        $product = Product::create([
            'slug' => Str::slug($productName) . '-' . Str::random(6),
            'type' => Product::TYPE_PRODUCT,
            'configuration_type' => 'variants',
            'is_active' => $this->faker->boolean(90),
            'brand_id' => $brands->random()->id,
            'department_id' => $departments->random()->id,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory ? $subCategory->id : null,
            'vendor_id' => $vendor->id,
            'created_by_user_id' => $vendor->user_id,
        ]);

        // Store translations
        foreach ($languages as $langCode => $language) {
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'title',
                'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'details',
                'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(3, true) : $this->fakerAr->paragraphs(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'summary',
                'lang_value' => $langCode === 'en' ? $this->faker->sentence(15) : $this->fakerAr->sentence(15),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'features',
                'lang_value' => $langCode === 'en' ? $this->generateFeatures() : $this->generateFeaturesAr(),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'instructions',
                'lang_value' => $langCode === 'en' ? $this->faker->paragraphs(2, true) : $this->fakerAr->paragraphs(2, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_title',
                'lang_value' => $langCode === 'en' ? $productName : $this->fakerAr->words(3, true),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_description',
                'lang_value' => $langCode === 'en' ? $this->faker->sentence(20) : $this->fakerAr->sentence(20),
            ]);
            $product->translations()->create([
                'lang_id' => $language->id,
                'lang_key' => 'meta_keywords',
                'lang_value' => $langCode === 'en' ? implode(', ', $this->faker->words(8)) : implode(', ', $this->fakerAr->words(8)),
            ]);
        }

        // Create vendor product
        $vendorProduct = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_active' => $this->faker->boolean(90),
            'status' => $this->faker->randomElement(['pending', 'approved', 'approved', 'approved']),
            'tax_id' => $taxes->random()->id,
            'sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'max_per_order' => $this->faker->numberBetween(1, 10),
            'is_active' => $product->is_active,
            'is_featured' => $this->faker->boolean(20),
        ]);

        // Get available variant configurations dynamically
        $variantKeys = VariantConfigurationKey::all();

        // Find the first key that has configurations to use for variants
        $primaryKey = null;
        $availableConfigs = collect();

        foreach ($variantKeys as $key) {
            $configs = VariantsConfiguration::where('key_id', $key->id)->get();
            if ($configs->isNotEmpty()) {
                $primaryKey = $key;
                $availableConfigs = $configs;
                break;
            }
        }

        // If no configurations found, skip variant creation
        if (!$primaryKey || $availableConfigs->isEmpty()) {
            echo "  ⚠️ No variant configurations found, skipping variant product\n";
            return;
        }

        // Select random configurations for this product
        $selectedConfigs = $availableConfigs->random($this->faker->numberBetween(1, min(3, $availableConfigs->count())));

        // Create variants
        foreach ($selectedConfigs as $config) {
            $configName = $config->getTranslation('name', 'en') ?? 'Variant';
            $variantTitle = $configName;

            $price = $this->faker->randomFloat(2, 50, 5000);
            $hasDiscount = $this->faker->boolean(30);

            // Create product variant with proper variant configuration ID
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'variant_configuration_id' => $config->id,
            ]);

            // Store variant translation
            foreach ($languages as $langCode => $language) {
                $translatedTitle = $config->getTranslation('name', $langCode) ?? $variantTitle;
                $variant->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'title',
                    'lang_value' => $translatedTitle,
                ]);
            }

            // Create vendor product variant
            $vendorProductVariant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProduct->id,
                'variant_configuration_id' => $config->id,
                'sku' => $vendorProduct->sku . '-' . strtoupper(substr($configName, 0, 3)),
                'price' => $price,
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? $price * $this->faker->randomFloat(2, 1.15, 1.5) : 0,
                'discount_end_date' => $hasDiscount ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            ]);

            // Create regional stock
            foreach ($regions->random($this->faker->numberBetween(2, min(5, $regions->count()))) as $region) {
                VendorProductVariantStock::create([
                    'vendor_product_variant_id' => $vendorProductVariant->id,
                    'region_id' => $region->id,
                    'quantity' => $this->faker->numberBetween(5, 200),
                ]);
            }
        }
    }

    /**
     * Generate a realistic product name
     */
    private function generateProductName(): string
    {
        $category = $this->faker->randomElement(array_keys($this->productCategories));
        $productType = $this->faker->randomElement($this->productCategories[$category]);
        $adjective = $this->faker->randomElement($this->adjectives);

        return "$adjective $productType";
    }

    /**
     * Generate product features in English
     */
    private function generateFeatures(): string
    {
        $features = [];
        for ($i = 0; $i < $this->faker->numberBetween(4, 8); $i++) {
            $features[] = "• " . $this->faker->sentence($this->faker->numberBetween(4, 8));
        }
        return implode("\n", $features);
    }

    /**
     * Generate product features in Arabic
     */
    private function generateFeaturesAr(): string
    {
        $features = [];
        for ($i = 0; $i < $this->faker->numberBetween(4, 8); $i++) {
            $features[] = "• " . $this->fakerAr->sentence($this->faker->numberBetween(4, 8));
        }
        return implode("\n", $features);
    }

    /**
     * Translate color to Arabic
     */
    private function translateColorToArabic(string $color): string
    {
        $translations = [
            'Black' => 'أسود',
            'White' => 'أبيض',
            'Red' => 'أحمر',
            'Blue' => 'أزرق',
            'Green' => 'أخضر',
            'Yellow' => 'أصفر',
            'Purple' => 'بنفسجي',
            'Orange' => 'برتقالي',
            'Pink' => 'وردي',
            'Gray' => 'رمادي',
            'Brown' => 'بني',
            'Navy' => 'كحلي',
            'Beige' => 'بيج',
        ];

        return $translations[$color] ?? $color;
    }
}
