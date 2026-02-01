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
                'type' => 'new',
                'color' => '#3498db',
                'sort_order' => 1,
                'names' => [
                    'en' => 'New',
                    'ar' => 'جديد'
                ]
            ],
            [
                'slug' => 'in-progress',
                'type' => 'in_progress',
                'color' => '#f1c40f',
                'sort_order' => 2,
                'names' => [
                    'en' => 'In Progress',
                    'ar' => 'قيد التنفيذ'
                ]
            ],
            [
                'slug' => 'deliver',
                'type' => 'deliver',
                'color' => '#2ecc71',
                'sort_order' => 3,
                'names' => [
                    'en' => 'Deliver',
                    'ar' => 'تم التوصيل'
                ]
            ],
            [
                'slug' => 'cancel',
                'type' => 'cancel',
                'color' => '#e74c3c',
                'sort_order' => 4,
                'names' => [
                    'en' => 'Cancel',
                    'ar' => 'ملغي'
                ]
            ]
        ];

        foreach ($stages as $stageData) {
            try {
                // Use updateOrCreate to avoid duplicate entry errors
                // Match on slug and country_id (or null) to handle the unique constraint properly
                $orderStage = OrderStage::updateOrCreate(
                    [
                        'slug' => $stageData['slug'],
                        'country_id' => null, // System stages have null country_id
                    ],
                    [
                        'type' => $stageData['type'],
                        'color' => $stageData['color'],
                        'active' => true,
                        'is_system' => true, // Mark as system stage (cannot be deleted)
                        'sort_order' => $stageData['sort_order'],
                    ]
                );

                $action = $orderStage->wasRecentlyCreated ? 'Created' : 'Updated';
                $this->command->info("{$action} order stage: {$stageData['names']['en']} (ID: {$orderStage->id})");

                // Add or update translations
                $translationsCreated = 0;
                foreach ($stageData['names'] as $langCode => $name) {
                    if (isset($languages[$langCode])) {
                        try {
                            $translation = $orderStage->translations()->updateOrCreate(
                                [
                                    'lang_id' => $languages[$langCode]->id,
                                    'lang_key' => 'name',
                                ],
                                [
                                    'lang_value' => $name,
                                ]
                            );
                            $translationsCreated++;
                            $this->command->info("  ✓ Translation {$langCode} => {$name}");
                        } catch (\Exception $e) {
                            $this->command->error("  ✗ Failed to create translation for {$langCode}: {$e->getMessage()}");
                        }
                    }
                }
                $this->command->info("  Total translations processed: {$translationsCreated}");
            } catch (\Exception $e) {
                $this->command->error("Error processing stage {$stageData['names']['en']}: {$e->getMessage()}");
            }
        }

        $this->command->info('Order stages seeded successfully!');
    }
}
