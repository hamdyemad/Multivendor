<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Language;
use Modules\CategoryManagment\app\Models\Activity;
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

        // Create Activities and Departments
        $this->createActivitiesAndDepartments($languages);

        // Create Categories and SubCategories
        $this->createCategoriesAndSubCategories($languages);

        // Create Brands
        $this->createBrands($languages);

        // Create Regions if not exist
        $this->createRegions($languages);

        echo "\n✅ Category & Department Seeder completed!\n";
    }

    /**
     * Create Activities and Departments
     */
    private function createActivitiesAndDepartments($languages)
    {
        echo "\n📁 Creating Activities and Departments...\n";

        foreach ($this->activitiesData as $activityNameEn => $activityData) {
            // Check if activity exists
            $activity = Activity::whereHas('translations', function($q) use ($activityNameEn) {
                $q->where('lang_key', 'name')->where('lang_value', $activityNameEn);
            })->first();

            if (!$activity) {
                $activity = Activity::create([
                    'slug' => Str::slug($activityNameEn),
                    'active' => true,
                ]);

                foreach ($languages as $langCode => $language) {
                    $activity->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $activityNameEn : $activityData['ar'],
                    ]);
                }
                echo "  ✓ Created activity: {$activityNameEn}\n";
            }

            // Create departments for this activity
            foreach ($activityData['departments'] as $deptData) {
                $department = Department::whereHas('translations', function($q) use ($deptData) {
                    $q->where('lang_key', 'name')->where('lang_value', $deptData['en']);
                })->first();

                if (!$department) {
                    $department = Department::create([
                        'slug' => Str::slug($deptData['en']),
                        'active' => true,
                    ]);

                    foreach ($languages as $langCode => $language) {
                        $department->translations()->create([
                            'lang_id' => $language->id,
                            'lang_key' => 'name',
                            'lang_value' => $langCode === 'en' ? $deptData['en'] : $deptData['ar'],
                        ]);
                    }

                    // Link department to activity
                    $department->activities()->attach($activity->id);
                    echo "    ✓ Created department: {$deptData['en']}\n";
                }
            }
        }
    }

    /**
     * Create Categories and SubCategories
     */
    private function createCategoriesAndSubCategories($languages)
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
                // Check if category exists
                $category = Category::whereHas('translations', function($q) use ($catData) {
                    $q->where('lang_key', 'name')->where('lang_value', $catData['en']);
                })->first();

                if (!$category) {
                    $category = Category::create([
                        'slug' => Str::slug($catData['en']),
                        'department_id' => $department->id,
                        'active' => true,
                    ]);

                    foreach ($languages as $langCode => $language) {
                        $category->translations()->create([
                            'lang_id' => $language->id,
                            'lang_key' => 'name',
                            'lang_value' => $langCode === 'en' ? $catData['en'] : $catData['ar'],
                        ]);
                    }
                    echo "  ✓ Created category: {$catData['en']}\n";
                }

                // Create subcategories
                if (isset($catData['subs'])) {
                    foreach ($catData['subs'] as $subData) {
                        $subCategory = SubCategory::whereHas('translations', function($q) use ($subData) {
                            $q->where('lang_key', 'name')->where('lang_value', $subData['en']);
                        })->first();

                        if (!$subCategory) {
                            $subCategory = SubCategory::create([
                                'slug' => Str::slug($subData['en']),
                                'category_id' => $category->id,
                                'active' => true,
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

    /**
     * Create Brands
     */
    private function createBrands($languages)
    {
        echo "\n🏷️ Creating Brands...\n";

        foreach ($this->brandsData as $brandData) {
            $brand = Brand::whereHas('translations', function($q) use ($brandData) {
                $q->where('lang_key', 'name')->where('lang_value', $brandData['en']);
            })->first();

            if (!$brand) {
                $brand = Brand::create([
                    'slug' => Str::slug($brandData['en']),
                    'active' => true,
                ]);

                foreach ($languages as $langCode => $language) {
                    $brand->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $brandData['en'] : $brandData['ar'],
                    ]);
                }
                echo "  ✓ Created brand: {$brandData['en']}\n";
            }
        }
    }

    /**
     * Create Regions if not exist
     */
    private function createRegions($languages)
    {
        echo "\n🌍 Checking Regions...\n";

        $regionCount = Region::count();
        if ($regionCount > 0) {
            echo "  ✓ Found {$regionCount} existing regions\n";
            return;
        }

        // Create default regions for each country
        $countries = Country::all();

        $defaultRegions = [
            ['en' => 'Central Region', 'ar' => 'المنطقة الوسطى'],
            ['en' => 'Eastern Region', 'ar' => 'المنطقة الشرقية'],
            ['en' => 'Western Region', 'ar' => 'المنطقة الغربية'],
            ['en' => 'Northern Region', 'ar' => 'المنطقة الشمالية'],
            ['en' => 'Southern Region', 'ar' => 'المنطقة الجنوبية'],
        ];

        foreach ($countries as $country) {
            // Get or create a default city
            $city = $country->cities()->first();

            if (!$city) {
                $city = City::create([
                    'country_id' => $country->id,
                    'active' => true,
                ]);

                foreach ($languages as $langCode => $language) {
                    $city->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? 'Main City' : 'المدينة الرئيسية',
                    ]);
                }
            }

            foreach ($defaultRegions as $regionData) {
                $region = Region::create([
                    'city_id' => $city->id,
                    'active' => true,
                ]);

                foreach ($languages as $langCode => $language) {
                    $region->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $langCode === 'en' ? $regionData['en'] : $regionData['ar'],
                    ]);
                }
                echo "  ✓ Created region: {$regionData['en']} for {$country->code}\n";
            }
        }
    }
}
