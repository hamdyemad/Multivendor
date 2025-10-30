<?php

namespace Modules\CatalogManagement\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Interfaces\VariantsConfigurationRepositoryInterface;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;

class VariantsConfigurationRepository implements VariantsConfigurationRepositoryInterface
{
    /**
     * Get all variants configurations with relationships
     */
    public function getAll()
    {
        return VariantsConfiguration::with(['key', 'parent_data', 'children'])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get variants configurations query for DataTables
     */
    public function getVariantsConfigurationsQuery(array $filters = [])
    {
        return VariantsConfiguration::with(['key', 'parent_data', 'children'])
            ->orderBy('id', 'desc');
    }

    /**
     * Find variants configuration by ID
     */
    public function findById(int $id)
    {
        return VariantsConfiguration::with(['key', 'parent_data', 'children'])->find($id);
    }

    /**
     * Create a new variants configuration
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $variantKey = VariantsConfiguration::create([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
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
     * Update variants configuration
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $variantKey = VariantsConfiguration::findOrFail($id);
            $variantKey->update([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
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
     * Delete variants configuration
     */
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $variantKey = VariantsConfiguration::findOrFail($id);
            $variantKey->translations()->delete();
            $variantKey->delete();
            return $variantKey;
        });
    }
}
