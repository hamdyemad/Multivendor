<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Language;
// Activity model removed
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;

class CategoryDepartmentSeeder extends Seeder
{
    /**
     * Activities with their departments
     */
    private $activitiesData = [
        'Electronics & Technology' => [
            'ar' => 'الإلكترونيات والتكنولوجيا',
            'departments' => [
                ['en' => 'Computers & Laptops', 'ar' => 'الحواسيب والأجهزة المحمولة'],
                ['en' => 'Mobile Phones', 'ar' => 'الهواتف المحمولة'],
                ['en' => 'Audio & Video', 'ar' => 'الصوتيات والمرئيات'],
                ['en' => 'Gaming', 'ar' => 'الألعاب'],
            ]
        ],
        'Fashion & Clothing' => [
            'ar' => 'الأزياء والملابس',
            'departments' => [
                ['en' => 'Men\'s Fashion', 'ar' => 'أزياء الرجال'],
                ['en' => 'Women\'s Fashion', 'ar' => 'أزياء النساء'],
                ['en' => 'Kids Fashion', 'ar' => 'أزياء الأطفال'],
                ['en' => 'Accessories', 'ar' => 'الإكسسوارات'],
            ]
        ],
        'Home & Garden' => [
            'ar' => 'المنزل والحديقة',
            'departments' => [
                ['en' => 'Furniture', 'ar' => 'الأثاث'],
                ['en' => 'Kitchen & Dining', 'ar' => 'المطبخ والطعام'],
                ['en' => 'Bedding & Bath', 'ar' => 'أغطية السرير والحمام'],
                ['en' => 'Garden & Outdoor', 'ar' => 'الحديقة والخارجية'],
            ]
        ],
        'Health & Beauty' => [
            'ar' => 'الصحة والجمال',
            'departments' => [
                ['en' => 'Skincare', 'ar' => 'العناية بالبشرة'],
                ['en' => 'Makeup', 'ar' => 'المكياج'],
                ['en' => 'Hair Care', 'ar' => 'العناية بالشعر'],
                ['en' => 'Fragrances', 'ar' => 'العطور'],
            ]
        ],
        'Sports & Fitness' => [
            'ar' => 'الرياضة واللياقة',
            'departments' => [
                ['en' => 'Sports Equipment', 'ar' => 'المعدات الرياضية'],
                ['en' => 'Fitness & Gym', 'ar' => 'اللياقة والجيم'],
                ['en' => 'Outdoor Sports', 'ar' => 'الرياضات الخارجية'],
                ['en' => 'Sports Wear', 'ar' => 'الملابس الرياضية'],
            ]
        ],
    ];

    /**
     * Categories for each department type
     */
    private $categoriesData = [
        'Computers & Laptops' => [
            ['en' => 'Laptops', 'ar' => 'أجهزة لابتوب', 'subs' => [
                ['en' => 'Gaming Laptops', 'ar' => 'لابتوب ألعاب'],
                ['en' => 'Business Laptops', 'ar' => 'لابتوب أعمال'],
                ['en' => 'Ultrabooks', 'ar' => 'ألترابوك'],
            ]],
            ['en' => 'Desktops', 'ar' => 'أجهزة مكتبية', 'subs' => [
                ['en' => 'All-in-One', 'ar' => 'الكل في واحد'],
                ['en' => 'Gaming PCs', 'ar' => 'حواسيب ألعاب'],
            ]],
            ['en' => 'Computer Accessories', 'ar' => 'ملحقات الحاسوب', 'subs' => [
                ['en' => 'Keyboards', 'ar' => 'لوحات مفاتيح'],
                ['en' => 'Mice', 'ar' => 'فأرات'],
                ['en' => 'Monitors', 'ar' => 'شاشات'],
            ]],
        ],
        'Mobile Phones' => [
            ['en' => 'Smartphones', 'ar' => 'هواتف ذكية', 'subs' => [
                ['en' => 'Android Phones', 'ar' => 'هواتف أندرويد'],
                ['en' => 'iPhones', 'ar' => 'آيفون'],
            ]],
            ['en' => 'Phone Accessories', 'ar' => 'ملحقات الهاتف', 'subs' => [
                ['en' => 'Cases & Covers', 'ar' => 'أغطية وحافظات'],
                ['en' => 'Chargers', 'ar' => 'شواحن'],
                ['en' => 'Screen Protectors', 'ar' => 'واقيات شاشة'],
            ]],
        ],
        'Men\'s Fashion' => [
            ['en' => 'Shirts', 'ar' => 'قمصان', 'subs' => [
                ['en' => 'Casual Shirts', 'ar' => 'قمصان كاجوال'],
                ['en' => 'Formal Shirts', 'ar' => 'قمصان رسمية'],
            ]],
            ['en' => 'Pants', 'ar' => 'بناطيل', 'subs' => [
                ['en' => 'Jeans', 'ar' => 'جينز'],
                ['en' => 'Chinos', 'ar' => 'تشينو'],
            ]],
            ['en' => 'Shoes', 'ar' => 'أحذية', 'subs' => [
                ['en' => 'Sneakers', 'ar' => 'سنيكرز'],
                ['en' => 'Formal Shoes', 'ar' => 'أحذية رسمية'],
            ]],
        ],
        'Women\'s Fashion' => [
            ['en' => 'Dresses', 'ar' => 'فساتين', 'subs' => [
                ['en' => 'Casual Dresses', 'ar' => 'فساتين كاجوال'],
                ['en' => 'Evening Dresses', 'ar' => 'فساتين سهرة'],
            ]],
            ['en' => 'Tops', 'ar' => 'بلوزات', 'subs' => [
                ['en' => 'Blouses', 'ar' => 'بلوزات'],
                ['en' => 'T-Shirts', 'ar' => 'تيشيرتات'],
            ]],
        ],
        'Furniture' => [
            ['en' => 'Living Room', 'ar' => 'غرفة المعيشة', 'subs' => [
                ['en' => 'Sofas', 'ar' => 'كنب'],
                ['en' => 'Coffee Tables', 'ar' => 'طاولات قهوة'],
            ]],
            ['en' => 'Bedroom', 'ar' => 'غرفة النوم', 'subs' => [
                ['en' => 'Beds', 'ar' => 'أسرة'],
                ['en' => 'Wardrobes', 'ar' => 'دواليب'],
            ]],
        ],
        'Skincare' => [
            ['en' => 'Face Care', 'ar' => 'العناية بالوجه', 'subs' => [
                ['en' => 'Moisturizers', 'ar' => 'مرطبات'],
                ['en' => 'Cleansers', 'ar' => 'منظفات'],
            ]],
            ['en' => 'Body Care', 'ar' => 'العناية بالجسم', 'subs' => [
                ['en' => 'Body Lotions', 'ar' => 'لوشن الجسم'],
                ['en' => 'Body Scrubs', 'ar' => 'مقشرات الجسم'],
            ]],
        ],
        'Sports Equipment' => [
            ['en' => 'Fitness Equipment', 'ar' => 'معدات اللياقة', 'subs' => [
                ['en' => 'Dumbbells', 'ar' => 'دمبلز'],
                ['en' => 'Treadmills', 'ar' => 'أجهزة مشي'],
            ]],
            ['en' => 'Team Sports', 'ar' => 'رياضات جماعية', 'subs' => [
                ['en' => 'Football', 'ar' => 'كرة قدم'],
                ['en' => 'Basketball', 'ar' => 'كرة سلة'],
            ]],
        ],
    ];

    /**
     * Brands data
     */
    private $brandsData = [
        ['en' => 'Apple', 'ar' => 'آبل'],
        ['en' => 'Samsung', 'ar' => 'سامسونج'],
        ['en' => 'Nike', 'ar' => 'نايكي'],
        ['en' => 'Adidas', 'ar' => 'أديداس'],
        ['en' => 'Sony', 'ar' => 'سوني'],
        ['en' => 'LG', 'ar' => 'إل جي'],
        ['en' => 'HP', 'ar' => 'إتش بي'],
        ['en' => 'Dell', 'ar' => 'ديل'],
        ['en' => 'Zara', 'ar' => 'زارا'],
        ['en' => 'H&M', 'ar' => 'إتش آند إم'],
        ['en' => 'IKEA', 'ar' => 'إيكيا'],
        ['en' => 'L\'Oreal', 'ar' => 'لوريال'],
        ['en' => 'Puma', 'ar' => 'بوما'],
        ['en' => 'Reebok', 'ar' => 'ريبوك'],
        ['en' => 'Lenovo', 'ar' => 'لينوفو'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 Starting Category & Department Seeder...\n";

        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        if ($languages->isEmpty()) {
            echo "❌ Error: No languages found. Please run LanguageSeeder first.\n";
            return;
        }

        // Get country_id from current country_code in session
        $countryCode = session('country_code', 'EG');
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = Country::first();
        }

        $countryId = $country ? $country->id : null;

        // Create Departments
        $this->createDepartments($languages, $countryId);

        // Create Categories and SubCategories
        $this->createCategoriesAndSubCategories($languages, $countryId);

        echo "\n✅ Category & Department Seeder completed!\n";
    }

    /**
     * Create Departments
     */
    private function createDepartments($languages, $countryId = null)
    {
        echo "\n📁 Creating Departments...\n";

        foreach ($this->activitiesData as $activityNameEn => $activityData) {
            
            // Create departments for this activity group (treating them as just departments now)
            foreach ($activityData['departments'] as $deptData) {
                // Generate base slug
                $slug = Str::slug($deptData['en']);
                $counter = 1;
                $originalSlug = $slug;

                // Keep incrementing counter until we find a unique slug
                while (Department::where('slug', $slug)
                    ->withoutCountryFilter()->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }


                $department = Department::create([
                    'slug' => $slug,
                    'active' => true,
                    'country_id' => $countryId,
                    'commission' => 15, // Default commission similar to old key
                ]);

                foreach ($languages as $langCode => $language) {
                    $department->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $deptData['en'] : $deptData['ar'],
                    ]);
                }

                echo "    ✓ Created department: {$deptData['en']}\n";
            }
        }
    }

    /**
     * Create Categories and SubCategories
     */
    private function createCategoriesAndSubCategories($languages, $countryId = null)
    {
        echo "\n📂 Creating Categories and SubCategories...\n";

        $departments = Department::all();

        foreach ($this->categoriesData as $deptNameEn => $categories) {
            // Find department
            $department = $departments->filter(function($d) use ($deptNameEn) {
                return $d->getTranslation('name', 'en') === $deptNameEn;
            })->first();

            if (!$department) {
                continue;
            }

            foreach ($categories as $catData) {
                // Generate base slug
                $slug = Str::slug($catData['en']);
                $counter = 1;
                $originalSlug = $slug;

                // Keep incrementing counter until we find a unique slug
                while (Category::where('slug', $slug)
                    ->withoutCountryFilter()->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }


                $category = Category::create([
                    'slug' => $slug,
                    'department_id' => $department->id,
                    'active' => true,
                    'country_id' => $countryId,
                ]);

                foreach ($languages as $langCode => $language) {
                    $category->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $catData['en'] : $catData['ar'],
                    ]);
                }
                echo "  ✓ Created category: {$catData['en']}\n";
                // Create subcategories
                if (isset($catData['subs'])) {
                    foreach ($catData['subs'] as $subData) {
                        // Generate base slug
                        $slug = Str::slug($subData['en']);
                        $counter = 1;
                        $originalSlug = $slug;

                        // Keep incrementing counter until we find a unique slug
                        while (SubCategory::where('slug', $slug)
                            ->withoutCountryFilter()->exists()) {
                            $slug = $originalSlug . '-' . $counter;
                            $counter++;
                        }


                        $subCategory = SubCategory::create([
                            'slug' => $slug,
                            'category_id' => $category->id,
                            'active' => true,
                            'country_id' => $countryId,
                        ]);

                        foreach ($languages as $langCode => $language) {
                            $subCategory->translations()->create([
                                'lang_id' => $language->id,
                                'lang_key' => 'name',
                                'lang_value' => $langCode === 'en' ? $subData['en'] : $subData['ar'],
                            ]);
                        }
                        echo "    ✓ Created subcategory: {$subData['en']}\n";
                    }
                }
            }
        }
    }

}
