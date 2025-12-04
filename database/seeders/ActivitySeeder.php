<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\CategoryManagment\app\Models\Activity;
use Faker\Factory as Faker;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        Activity::factory()
            ->count(10) // Reasonable count
            ->create()
            ->each(function ($activity) use ($faker) {
                $activity->setTranslation('name', 'en', $faker->words(2, true));
                $activity->setTranslation('name', 'ar', 'نشاط ' . $faker->word());
                $activity->save();
            });
    }
}
