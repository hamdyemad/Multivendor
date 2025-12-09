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
        $query = BundleCategory::with(['translations'])->filter($filters)->latest();
        return $query;
    }


    public function getAll(array $filters = [], $per_page = 10)
    {
        $query = BundleCategory::with(['translations'])
        ->filter($filters);
        return ($per_page == 0) ? $query->get() : $query->paginate($per_page);
    }


    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        return BundleCategory::with(['translations', 'attachments'])
        ->where('slug', $id)
        ->orWhere('id', $id)->first();
    }

    /**
     * Create new bundle category
     */
    public function createBundleCategory(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                Log::info('Creating bundle category', ['data' => $data]);

                $bundleCategory = BundleCategory::create([
                    'slug' => uniqid(),
                    'active' => $data['active'] ?? 1,
                ]);


                // Store translations (this will update the slug based on English name)
                $this->storeTranslations($bundleCategory, $data);


                // Handle image upload
                $this->handleImage($bundleCategory, $data);


                return $bundleCategory;
            });
        } catch (\Exception $e) {
            Log::error('Error creating bundle category', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                $oldImage = $bundleCategory->attachments()->where('type', 'image')->first();
                if ($oldImage) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldImage->path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage->path);
                    }
                    $oldImage->delete();
                }

                // Store new image
                $this->handleImage($bundleCategory, $data);
            }

            return $bundleCategory->fresh();
        });
    }

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id)
    {
        return DB::transaction(function () use ($id) {
            $bundleCategory = $this->getBundleCategoryById($id);

            // Delete attachments
            foreach ($bundleCategory->attachments as $attachment) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($attachment->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->path);
                }
                $attachment->delete();
            }

            return $bundleCategory->delete();
        });
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

    /**
     * Handle image upload for bundle category
     */
    protected function handleImage(BundleCategory $bundleCategory, array $data): void
    {
        if (isset($data['image']) && $data['image']) {
            $path = $data['image']->store("bundle-categories/{$bundleCategory->id}", 'public');
            $bundleCategory->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }
    }
}
