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
        return VariantsConfiguration::with([
            'translations',
            'key.translations',
            'parent_data',
            'children'
        ])->find($id);
    }

    /**
     * Create a new variants configuration
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create the variant configuration
            $variantConfig = VariantsConfiguration::create([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name']) && !empty($translation['name'])) {
                        $variantConfig->translations()->updateOrCreate([
                            'lang_id' => (int) $langId,
                            'lang_key' => 'name',
                        ], [
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            // Load relationships for return
            return $variantConfig->load(['translations', 'key', 'parent_data']);
        });
    }

    /**
     * Update variants configuration
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            \Log::info('VariantsConfiguration Update Request', [
                'id' => $id,
                'data' => $data
            ]);
            $variant = VariantsConfiguration::findOrFail($id);
            $variant->update([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name']) && !empty($translation['name'])) {
                        $variant->translations()->updateOrCreate([
                            'lang_id' => (int) $langId,
                            'lang_key' => 'name',
                        ], [
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            return $variant;
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

    /**
     * Get parent variants by key ID
     *
     * @param int $keyId
     * @param int|null $currentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentsByKey($keyId, $currentId = null)
    {
        $query = VariantsConfiguration::with(['key', 'parent_data.translations', 'translations'])
            ->where('key_id', $keyId);

        // Exclude current variant to prevent self-referencing
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get variant configuration keys for API
     *
     * @return array
     */
    public function getVariantKeysForApi()
    {
        $keys = \Modules\CatalogManagement\app\Models\VariantConfigurationKey::with('translations')
            ->get();

        \Log::info('Found keys count: ' . $keys->count());

        return $keys->map(function ($key) {
            return [
                'id' => $key->id,
                'name' => $key->getTranslation('name', app()->getLocale()) ?? 'No Name',
            ];
        })->toArray();
    }

    /**
     * Get variants by key ID for API
     *
     * @param int $keyId
     * @param string|null $parentId
     * @return array
     */
    public function getVariantsByKeyForApi($keyId, $parentId = null)
    {
        $query = VariantsConfiguration::with(['translations', 'children.translations'])
            ->where('key_id', $keyId);

        // If parent_id is provided, get children of that parent
        // If parent_id is null or 'root', get root variants (no parent)
        if ($parentId && $parentId !== 'root') {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $variants = $query->get();

        return $variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->getTranslation('name', app()->getLocale()) ?? $variant->value,
                'value' => $variant->value,
                'has_children' => $variant->children->count() > 0,
                'children_count' => $variant->children->count()
            ];
        })->toArray();
    }
}
