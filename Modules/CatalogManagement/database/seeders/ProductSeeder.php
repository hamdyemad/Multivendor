<?php

namespace Modules\CatalogManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\AreaSettings\app\Models\Region;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use App\Models\Language;

class ProductSeeder extends Seeder
{
    private $englishLangId;
    private $arabicLangId;
    private $user;
    private $vendor;
    private $region;

    /**
     * Get language ID by code
     */
    private function getLanguageId(string $code): int
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            throw new \Exception("Language with code '{$code}' not found");
        }
        return $language->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize
        $this->englishLangId = $this->getLanguageId('en');
        $this->arabicLangId = $this->getLanguageId('ar');
        $this->user = \App\Models\User::first();
        $this->vendor = \Modules\Vendor\app\Models\Vendor::first();
        $this->region = Region::first();

        if (!$this->user) {
            $this->command->error('No user found');
            return;
        }
        if (!$this->vendor) {
            $this->command->error('No vendor found');
            return;
        }
        if (!$this->region) {
            $this->command->error('No region found');
            return;
        }

        // Clear existing data
        $this->clearExistingData();

        // Create Departments
        $departments = $this->seedDepartments();

        // Create Categories
        $categories = $this->seedCategories($departments);

        // Create SubCategories
        $subCategories = $this->seedSubCategories($categories);

        // Create Brands
        $brands = $this->seedBrands();

        // Create Tax
        $taxes = $this->seedTaxes();

        // Create Variant Configuration Keys
        $variantKeys = $this->seedVariantKeys();

        // Create Products with variants
        $this->seedProducts($departments, $categories, $subCategories, $brands, $taxes, $variantKeys);

        $this->command->info('✓ Product seeding completed successfully!');
    }

    /**
     * Clear existing product data
     */
    private function clearExistingData(): void
    {
        $this->command->info('Clearing existing product data...');

        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear in correct order (child tables first)
        DB::table('vendor_product_variant_stocks')->truncate();
        DB::table('vendor_product_variants')->truncate();
        DB::table('vendor_products')->truncate();
        DB::table('product_variants')->truncate();
        DB::table('products')->truncate();
        DB::table('brands')->truncate();
        DB::table('taxes')->truncate();

        // Only truncate if table exists
        if (DB::getSchemaBuilder()->hasTable('variants_configurations')) {
            DB::table('variants_configurations')->truncate();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Cleared existing data');
    }

    /**
     * Seed departments
     */
    private function seedDepartments()
    {
        $departments = [
            ['name_en' => 'Electronics', 'name_ar' => 'الإلكترونيات'],
            ['name_en' => 'Fashion', 'name_ar' => 'الموضة'],
            ['name_en' => 'Home & Garden', 'name_ar' => 'المنزل والحديقة'],
        ];

        $created = [];
        foreach ($departments as $dept) {
            $department = Department::create(['active' => true]);

            $department->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $dept['name_en'],
            ]);

            $department->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $dept['name_ar'],
            ]);

            $created[] = $department;
        }

        $this->command->info('✓ Created ' . count($created) . ' departments');
        return $created;
    }

    /**
     * Seed categories
     */
    private function seedCategories($departments)
    {
        $categories = [];

        $categoryData = [
            ['name_en' => 'Smartphones', 'name_ar' => 'الهواتف الذكية', 'dept' => 0],
            ['name_en' => 'Laptops', 'name_ar' => 'أجهزة الكمبيوتر المحمولة', 'dept' => 0],
            ['name_en' => 'Men Clothing', 'name_ar' => 'ملابس الرجال', 'dept' => 1],
            ['name_en' => 'Women Clothing', 'name_ar' => 'ملابس النساء', 'dept' => 1],
            ['name_en' => 'Furniture', 'name_ar' => 'الأثاث', 'dept' => 2],
        ];

        foreach ($categoryData as $cat) {
            $category = Category::create([
                'department_id' => $departments[$cat['dept']]->id,
                'active' => true,
            ]);

            $category->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_en'],
            ]);

            $category->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_ar'],
            ]);

            $categories[] = $category;
        }

        $this->command->info('✓ Created ' . count($categories) . ' categories');
        return $categories;
    }

    /**
     * Seed subcategories
     */
    private function seedSubCategories($categories)
    {
        $subCategories = [];

        foreach ($categories as $category) {
            $subCat = SubCategory::create(['category_id' => $category->id, 'active' => true]);

            $subCat->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => 'Sub ' . $category->getTranslation('name', 'en'),
            ]);

            $subCat->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => 'فئة فرعية ' . $category->getTranslation('name', 'ar'),
            ]);

            $subCategories[] = $subCat;
        }

        $this->command->info('✓ Created ' . count($subCategories) . ' subcategories');
        return $subCategories;
    }

    /**
     * Seed brands
     */
    private function seedBrands()
    {
        $brands = [
            ['name_en' => 'Samsung', 'name_ar' => 'سامسونج', 'slug' => 'samsung'],
            ['name_en' => 'Apple', 'name_ar' => 'أبل', 'slug' => 'apple'],
            ['name_en' => 'Dell', 'name_ar' => 'ديل', 'slug' => 'dell'],
            ['name_en' => 'Nike', 'name_ar' => 'نايك', 'slug' => 'nike'],
            ['name_en' => 'IKEA', 'name_ar' => 'إيكيا', 'slug' => 'ikea'],
        ];

        $created = [];
        foreach ($brands as $brand) {
            $brandModel = Brand::create(['slug' => $brand['slug'], 'active' => true]);

            $brandModel->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $brand['name_en'],
            ]);

            $brandModel->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $brand['name_ar'],
            ]);

            $created[] = $brandModel;
        }

        $this->command->info('✓ Created ' . count($created) . ' brands');
        return $created;
    }

    /**
     * Seed taxes
     */
    private function seedTaxes()
    {
        $taxes = [
            ['name_en' => 'Standard Tax', 'name_ar' => 'الضريبة القياسية', 'slug' => 'standard-tax', 'rate' => 15.00],
            ['name_en' => 'Reduced Tax', 'name_ar' => 'الضريبة المخفضة', 'slug' => 'reduced-tax', 'rate' => 5.00],
            ['name_en' => 'Premium Tax', 'name_ar' => 'ضريبة الفئة الممتازة', 'slug' => 'premium-tax', 'rate' => 20.00],
        ];

        $created = [];
        foreach ($taxes as $tax) {
            $taxModel = Tax::create(['slug' => $tax['slug'], 'tax_rate' => $tax['rate'], 'active' => true]);

            $taxModel->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $tax['name_en'],
            ]);

            $taxModel->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $tax['name_ar'],
            ]);

            $created[] = $taxModel;
        }

        $this->command->info('✓ Created ' . count($created) . ' taxes');
        return $created;
    }

    /**
     * Seed variant configuration keys and values
     */
    private function seedVariantKeys()
    {
        $colors = ['Red', 'Blue', 'Green', 'Black'];
        $colorConfigs = [];

        // Get or create Color key
        $colorKey = VariantConfigurationKey::firstOrCreate(
            ['id' => 1],
            []
        );

        // Add color key translation if needed
        if (!$colorKey->translations()->where('lang_id', $this->englishLangId)->exists()) {
            $colorKey->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => 'Color',
            ]);
            $colorKey->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => 'اللون',
            ]);
        }

        // Create color configurations
        foreach ($colors as $color) {
            $config = VariantsConfiguration::create([
                'key_id' => $colorKey->id,
                'parent_id' => null,
            ]);

            // Add color translation
            $config->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'color',
                'lang_value' => $color,
            ]);
            $config->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'color',
                'lang_value' => $this->getArabicColor($color),
            ]);

            $colorConfigs[] = $config;
        }

        $this->command->info('✓ Created ' . count($colorConfigs) . ' variant configurations');
        return $colorConfigs;
    }

    /**
     * Seed products
     */
    private function seedProducts($departments, $categories, $subCategories, $brands, $taxes, $variantKeys)
    {
        $products = [
            // SIMPLE PRODUCTS (no variants) - NO DISCOUNT
            [
                'title_en' => 'Samsung Galaxy S21',
                'title_ar' => 'سامسونج جالاكسي S21',
                'slug' => 'samsung-galaxy-s21',
                'brand_id' => $brands[0]->id,
                'category_id' => $categories[0]->id,
                'type' => 'simple',
                'has_discount' => false,
                'price' => 899.99,
                'stock' => 50,
            ],
            [
                'title_en' => 'Apple iPhone 13',
                'title_ar' => 'أبل آيفون 13',
                'slug' => 'apple-iphone-13',
                'brand_id' => $brands[1]->id,
                'category_id' => $categories[0]->id,
                'type' => 'simple',
                'has_discount' => false,
                'price' => 999.99,
                'stock' => 30,
            ],

            // SIMPLE PRODUCTS - WITH DISCOUNT (active)
            [
                'title_en' => 'Dell XPS 13 Laptop',
                'title_ar' => 'جهاز كمبيوتر ديل XPS 13',
                'slug' => 'dell-xps-13-laptop',
                'brand_id' => $brands[2]->id,
                'category_id' => $categories[1]->id,
                'type' => 'simple',
                'has_discount' => true,
                'price' => 899.99,
                'price_before_discount' => 1299.99,
                'discount_end_date' => now()->addDays(30),
                'stock' => 20,
            ],
            [
                'title_en' => 'Apple MacBook Pro 14',
                'title_ar' => 'أبل ماك بوك برو 14',
                'slug' => 'apple-macbook-pro-14',
                'brand_id' => $brands[1]->id,
                'category_id' => $categories[1]->id,
                'type' => 'simple',
                'has_discount' => true,
                'price' => 1799.99,
                'price_before_discount' => 1999.99,
                'discount_end_date' => now()->addDays(15),
                'stock' => 15,
            ],

            // SIMPLE PRODUCTS - WITH EXPIRED DISCOUNT
            [
                'title_en' => 'Nike Running Shoes',
                'title_ar' => 'حذاء الجري من نايك',
                'slug' => 'nike-running-shoes',
                'brand_id' => $brands[3]->id,
                'category_id' => $categories[2]->id,
                'type' => 'simple',
                'has_discount' => true,
                'price' => 89.99,
                'price_before_discount' => 129.99,
                'discount_end_date' => now()->subDays(5),  // Expired
                'stock' => 100,
            ],

            // VARIANT PRODUCTS - NO DISCOUNT
            [
                'title_en' => 'Samsung Galaxy A52',
                'title_ar' => 'سامسونج جالاكسي A52',
                'slug' => 'samsung-galaxy-a52',
                'brand_id' => $brands[0]->id,
                'category_id' => $categories[0]->id,
                'type' => 'variants',
                'has_discount' => false,
                'variants' => ['Red', 'Blue', 'Green', 'Black'],
                'base_price' => 449.99,
                'stock_per_variant' => 25,
            ],

            // VARIANT PRODUCTS - WITH DISCOUNT (all variants)
            [
                'title_en' => 'Nike Air Max 90',
                'title_ar' => 'نايك إير ماكس 90',
                'slug' => 'nike-air-max-90',
                'brand_id' => $brands[3]->id,
                'category_id' => $categories[2]->id,
                'type' => 'variants',
                'has_discount' => true,
                'variants' => ['White', 'Black', 'Red', 'Blue'],
                'base_price' => 129.99,
                'price_before_discount' => 179.99,
                'discount_end_date' => now()->addDays(20),
                'stock_per_variant' => 40,
            ],

            // VARIANT PRODUCTS - MIXED DISCOUNT (some with, some without)
            [
                'title_en' => 'IKEA Modern Sofa',
                'title_ar' => 'أريكة إيكيا الحديثة',
                'slug' => 'ikea-modern-sofa',
                'brand_id' => $brands[4]->id,
                'category_id' => $categories[4]->id,
                'type' => 'variants_mixed',
                'variants' => ['Gray', 'Brown', 'Black', 'Beige'],
                'base_price' => 599.99,
                'stock_per_variant' => 10,
            ],
        ];

        $createdCount = 0;
        foreach ($products as $productData) {
            $subCategoryId = !empty($subCategories) ? $subCategories[0]->id : 1;
            $product = Product::create([
                'brand_id' => $productData['brand_id'],
                'department_id' => $departments[0]->id,  // Use first department
                'category_id' => $productData['category_id'],
                'sub_category_id' => $subCategoryId,
                'created_by_user_id' => $this->user->id,
                'is_active' => true,
                'configuration_type' => $productData['type'] === 'simple' ? 'simple' : 'variants',
            ]);

            // Add translation
            $product->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'title',
                'lang_value' => $productData['title_en'],
            ]);

            $product->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'title',
                'lang_value' => $productData['title_ar'],
            ]);

            // Create vendor product with random status, sales, and views
            $statuses = ['pending', 'approved', 'rejected'];
            $randomStatus = $statuses[array_rand($statuses)];

            $vendorProduct = VendorProduct::create([
                'vendor_id' => $this->vendor->id,
                'product_id' => $product->id,
                'tax_id' => $taxes[0]->id,
                'sku' => $productData['slug'] . '-sku',
                'max_per_order' => 10,
                'is_active' => true,
                'is_featured' => rand(0, 1) ? true : false,
                'status' => $randomStatus,
                'sales' => rand(0, 500),
                'views' => rand(100, 5000),
            ]);

            // Create product variants first
            $productVariants = [];
            if ($productData['type'] === 'simple') {
                // Simple product - create one variant
                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_configuration_id' => $variantKeys[0]->id,
                ]);
                $productVariants[] = $productVariant;
            } else {
                // Variant products - create one variant per color
                $variants = $productData['variants'];
                foreach ($variants as $variantName) {
                    $productVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_configuration_id' => $variantKeys[0]->id,
                    ]);
                    $productVariants[] = $productVariant;
                }
            }

            // Create vendor product variants with pricing
            if ($productData['type'] === 'simple') {
                $this->createSimpleProductVariant(
                    $vendorProduct,
                    $productData,
                    $productVariants[0],
                    $variantKeys[0]
                );
            } else {
                $variants = $productData['variants'];
                foreach ($variants as $index => $variantName) {
                    $hasDiscount = $productData['type'] === 'variants_mixed' ? ($index % 2 === 0) : $productData['has_discount'];

                    $this->createVariantProduct(
                        $vendorProduct,
                        $productData,
                        $variantName,
                        $hasDiscount,
                        $productVariants[$index],
                        $variantKeys[0]
                    );
                }
            }

            $createdCount++;
            $this->command->info("✓ Created: {$productData['title_en']}");
        }

        $this->command->info("✓ Created {$createdCount} products with variants");
    }

    /**
     * Create a simple product variant (no color/size variations)
     */
    private function createSimpleProductVariant($vendorProduct, $productData, $productVariant, $colorKey)
    {
        $hasDiscount = $productData['has_discount'] ?? false;
        $priceBeforeDiscount = $hasDiscount ? ($productData['price_before_discount'] ?? $productData['price'] * 1.3) : $productData['price'];

        $vendorProductVariant = VendorProductVariant::create([
            'vendor_product_id' => $vendorProduct->id,
            'variant_configuration_id' => $colorKey->id,
            'sku' => $vendorProduct->sku . '-simple',
            'price' => $productData['price'],
            'has_discount' => $hasDiscount,
            'price_before_discount' => $priceBeforeDiscount,
            'discount_end_date' => $hasDiscount ? ($productData['discount_end_date'] ?? now()->addDays(30)) : null,
        ]);

        // Create stock
        VendorProductVariantStock::create([
            'vendor_product_variant_id' => $vendorProductVariant->id,
            'region_id' => $this->region->id,
            'quantity' => $productData['stock'],
        ]);
    }

    /**
     * Create a variant product (with color/size variations)
     */
    private function createVariantProduct($vendorProduct, $productData, $variantName, $hasDiscount, $productVariant, $colorKey)
    {
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Brown', 'Gray', 'Beige'];
        $colorIndex = array_search($variantName, $colors);

        $priceBeforeDiscount = $hasDiscount ? ($productData['price_before_discount'] ?? $productData['base_price'] * 1.3) : $productData['base_price'];

        $vendorProductVariant = VendorProductVariant::create([
            'vendor_product_id' => $vendorProduct->id,
            'variant_configuration_id' => $colorKey->id,
            'sku' => $vendorProduct->sku . '-' . strtolower($variantName),
            'price' => $productData['base_price'],
            'has_discount' => $hasDiscount,
            'price_before_discount' => $priceBeforeDiscount,
            'discount_end_date' => $hasDiscount ? ($productData['discount_end_date'] ?? now()->addDays(30)) : null,
        ]);

        // Create stock
        VendorProductVariantStock::create([
            'vendor_product_variant_id' => $vendorProductVariant->id,
            'region_id' => $this->region->id,
            'quantity' => $productData['stock_per_variant'],
        ]);
    }

    /**
     * Get Arabic color name
     */
    private function getArabicColor(string $color): string
    {
        $colors = [
            'Red' => 'أحمر',
            'Blue' => 'أزرق',
            'Green' => 'أخضر',
            'Black' => 'أسود',
            'White' => 'أبيض',
            'Brown' => 'بني',
            'Gray' => 'رمادي',
            'Beige' => 'بيج',
        ];
        return $colors[$color] ?? $color;
    }
}
