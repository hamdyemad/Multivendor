<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\BundleCategoryRepositoryInterface;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BundleCategoryRepository implements BundleCategoryRepositoryInterface
{
    /**
     * Get all bundle categories with optional filters
     */
    public function getBundleCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        $query = BundleCategory::with(['translations'])->filter($filters);
        return $query;
    }

    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        return BundleCategory::with(['translations', 'attachments'])->findOrFail($id);
    }

    /**
     * Create new bundle category
     */
    public function createBundleCategory(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bundleCategory = BundleCategory::create([
                'slug' => $data['slug'] ?? null,
                'active' => $data['active'] ?? 1,
            ]);

            // Store translations
            $this->storeTranslations($bundleCategory, $data);

            return $bundleCategory;
        });
    }

    /**
     * Update bundle category
     */
    public function updateBundleCategory($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bundleCategory = $this->getBundleCategoryById($id);

            $bundleCategory->update([
                'active' => $data['active'] ?? $bundleCategory->active,
            ]);

            // Store translations (this will handle updates)
            $this->storeTranslations($bundleCategory, $data);

            return $bundleCategory->fresh();
        });
    }

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id)
    {
        $bundleCategory = $this->getBundleCategoryById($id);
        return $bundleCategory->delete();
    }

    /**
     * Get active bundle categories
     */
    public function getActiveBundleCategories()
    {
        return BundleCategory::active()->with(['translations'])->get();
    }

    /**
     * Toggle bundle category status
     */
    public function toggleBundleCategoryStatus($id)
    {
        $bundleCategory = $this->getBundleCategoryById($id);
        $bundleCategory->update(['active' => !$bundleCategory->active]);
        return $bundleCategory->fresh();
    }

    /**
     * Store translations for bundle category
     */
    protected function storeTranslations(BundleCategory $bundleCategory, array $data): void
    {
        // Force delete existing translations (including soft deleted ones)
        $bundleCategory->translations()->forceDelete();

        if (!empty($data['translations'])) {
            Log::info('Storing translations for bundle category', [
                'bundle_category_id' => $bundleCategory->id,
                'translations_data' => $data['translations']
            ]);

            foreach ($data['translations'] as $languageId => $fields) {
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store all translation fields
                $translationFields = [
                    'name', 'seo_title', 'seo_description', 'seo_keywords'
                ];

                foreach ($translationFields as $field) {
                    if (isset($fields[$field])) {

                        if($field == 'name' && $language->code == 'en') {
                            // Generate slug from English name
                            $model = BundleCategory::where('slug', Str::slug($fields[$field]))
                            ->where('id', '!=', $bundleCategory->id)
                            ->withoutCountryFilter()
                            ->first();
                            if($model) {
                                $newSlug = $model->slug . '-' . rand(1, 1000);
                                $bundleCategory->update([
                                    'slug' => $newSlug
                                ]);
                            } else {
                                $bundleCategory->update([
                                    'slug' => Str::slug($fields[$field])
                                ]);
                            }
                        }

                        Log::info('Creating bundle category translation', [
                            'field' => $field,
                            'language' => $language->code,
                            'value' => $fields[$field]
                        ]);

                        $bundleCategory->translations()->create([
                            'lang_id' => $language->id,
                            'lang_key' => $field,
                            'lang_value' => $fields[$field],
                        ]);
                    }
                }
            }
        }
    }
}
