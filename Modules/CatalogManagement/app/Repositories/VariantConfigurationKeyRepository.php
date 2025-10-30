<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\VariantConfigurationKeyRepositoryInterface;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Illuminate\Support\Facades\DB;

class VariantConfigurationKeyRepository implements VariantConfigurationKeyRepositoryInterface
{
    /**
     * Get all variant configuration keys with filters and pagination
     */
    public function getAllVariantConfigurationKeys($filters, $perPage = 10)
    {
        $query = VariantConfigurationKey::with('translations', 'parent')->filter($filters);
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get variant configuration keys query for DataTables
     */
    public function getVariantConfigurationKeysQuery(array $filters = [])
    {
        return VariantConfigurationKey::with('translations', 'parent')->filter($filters);
    }
    /**
     * Find variant configuration key by ID
     */
    public function findById(int $id)
    {
        return VariantConfigurationKey::with('translations', 'parent')->findOrFail($id);
    }

    /**
     * Create a new variant configuration key
     */
    public function createVariantConfigurationKey(array $data)
    {
        return DB::transaction(function () use ($data) {
            $variantKey = VariantConfigurationKey::create([
                'parent_key_id' => $data['parent_key_id'] ?? null,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $variantKey->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            return $variantKey;
        });
    }

    /**
     * Update variant configuration key
     */
    public function updateVariantConfigurationKey(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $variantKey = VariantConfigurationKey::findOrFail($id);

            $variantKey->update([
                'parent_key_id' => $data['parent_key_id'] ?? null,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $variantKey->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                }
            }
            return $variantKey;
        });
    }

    /**
     * Delete variant configuration key
     */
    public function deleteVariantConfigurationKey(int $id)
    {
        $variantKey = VariantConfigurationKey::findOrFail($id);
        $variantKey->translations()->delete();
        return $variantKey->delete();
    }
}
