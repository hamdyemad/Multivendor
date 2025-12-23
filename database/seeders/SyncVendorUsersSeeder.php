<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Modules\Vendor\app\Models\Vendor;

class SyncVendorUsersSeeder extends Seeder
{
    /**
     * Sync existing vendors' user accounts with vendor role, name, and image.
     */
    public function run(): void
    {
        $this->command->info('Starting vendor users sync...');

        // Get the vendor role
        $vendorRole = Role::where('type', Role::VENDOR_ROLE_TYPE)->first();

        if (!$vendorRole) {
            $this->command->error('Vendor role not found! Please ensure the vendor role exists.');
            return;
        }

        // Get all vendors with their users, translations, and logo
        $vendors = Vendor::with(['user', 'translations', 'logo'])->get();

        $updated = 0;
        $skipped = 0;

        foreach ($vendors as $vendor) {
            if (!$vendor->user) {
                $this->command->warn("Vendor #{$vendor->id} has no associated user. Skipping...");
                $skipped++;
                continue;
            }

            $user = $vendor->user;

            // 1. Assign vendor role if not already assigned
            if (!$user->roles()->where('roles.id', $vendorRole->id)->exists()) {
                $user->roles()->syncWithoutDetaching([$vendorRole->id]);
                $this->command->info("  - Assigned vendor role to user #{$user->id}");
            }

            // 2. Sync name translations from vendor to user
            foreach ($vendor->translations as $translation) {
                if ($translation->lang_key === 'name') {
                    $user->translations()->updateOrCreate(
                        [
                            'lang_id' => $translation->lang_id,
                            'lang_key' => 'name',
                        ],
                        [
                            'lang_value' => $translation->lang_value,
                        ]
                    );
                }
            }

            // 3. Sync logo image to user
            if ($vendor->logo && $vendor->logo->path) {
                $user->update(['image' => $vendor->logo->path]);
            }

            $this->command->info("Updated user #{$user->id} for vendor #{$vendor->id} ({$vendor->name})");
            $updated++;
        }

        $this->command->info('');
        $this->command->info("Sync completed! Updated: {$updated}, Skipped: {$skipped}");
    }
}
