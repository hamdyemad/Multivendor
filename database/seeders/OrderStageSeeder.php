<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\app\Models\OrderStage;
use App\Models\Language;
use Illuminate\Support\Str;

class OrderStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get English and Arabic languages
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        if ($languages->isEmpty()) {
            $this->command->error('Languages not found. Please seed languages first.');
            return;
        }

        $stages = [
            [
                'slug' => 'new',
                'color' => '#3498db',
                'sort_order' => 1,
                'names' => [
                    'en' => 'New',
                    'ar' => 'جديد'
                ]
            ],
            [
                'slug' => 'in-progress',
                'color' => '#f1c40f',
                'sort_order' => 2,
                'names' => [
                    'en' => 'In Progress',
                    'ar' => 'قيد التنفيذ'
                ]
            ],
            [
                'slug' => 'deliver',
                'color' => '#2ecc71',
                'sort_order' => 3,
                'names' => [
                    'en' => 'Deliver',
                    'ar' => 'تم التوصيل'
                ]
            ],
            [
                'slug' => 'cancel',
                'color' => '#e74c3c',
                'sort_order' => 4,
                'names' => [
                    'en' => 'Cancel',
                    'ar' => 'ملغي'
                ]
            ],
            [
                'slug' => 'want-to-return',
                'color' => '#e67e22',
                'sort_order' => 5,
                'names' => [
                    'en' => 'Want To Return',
                    'ar' => 'يريد الإرجاع'
                ]
            ],
            [
                'slug' => 'in-progress-return',
                'color' => '#9b59b6',
                'sort_order' => 6,
                'names' => [
                    'en' => 'In Progress Return',
                    'ar' => 'قيد الإرجاع'
                ]
            ],
            [
                'slug' => 'refund',
                'color' => '#1abc9c',
                'sort_order' => 7,
                'names' => [
                    'en' => 'Refund',
                    'ar' => 'مسترد'
                ]
            ],
        ];

        foreach ($stages as $stageData) {

                $slug = Str::slug($stageData['names']['en']);
                $counter = 1;
                $originalSlug = $slug;

                // Keep incrementing counter until we find a unique slug
                while (OrderStage::where('slug', $slug)
                    ->withoutCountryFilter()->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

            try {
                // Create the order stage
                $orderStage = OrderStage::create([
                    'slug' => $slug,
                    'color' => $stageData['color'],
                    'active' => true,
                    'is_system' => true, // Mark as system stage (cannot be deleted)
                    'sort_order' => $stageData['sort_order'],
                ]);

                $this->command->info("Created order stage: {$stageData['names']['en']} (ID: {$orderStage->id})");

                // Add translations
                $translationsCreated = 0;
                foreach ($stageData['names'] as $langCode => $name) {
                    if (isset($languages[$langCode])) {
                        try {
                            $translation = $orderStage->translations()->create([
                                'lang_id' => $languages[$langCode]->id,
                                'lang_key' => 'name',
                                'lang_value' => $name,
                            ]);
                            $translationsCreated++;
                            $this->command->info("  ✓ Translation created: {$langCode} => {$name}");
                        } catch (\Exception $e) {
                            $this->command->error("  ✗ Failed to create translation for {$langCode}: {$e->getMessage()}");
                        }
                    }
                }
                $this->command->info("  Total translations created: {$translationsCreated}");
            } catch (\Exception $e) {
                $this->command->error("Error creating stage {$stageData['names']['en']}: {$e->getMessage()}");
            }
        }

        $this->command->info('Order stages seeded successfully!');
    }
}
