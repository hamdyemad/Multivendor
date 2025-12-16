<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\BlogCategory;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

use Modules\SystemSetting\app\Interfaces\BlogCategoryRepositoryInterface;

class BlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    /**
     * Get all blog categories.
     */
    public function getAll($filters = [])
    {
        return BlogCategory::with(['translations', 'attachments'])->filter($filters)->get();
    }

    /**
     * Get blog categories for datatable.
     */
    public function getDatatable($request)
    {
        $query = BlogCategory::with(['translations', 'attachments']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('active') && $request->active !== '') {
            $query->where('active', $request->active);
        }

        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        return $query;
    }

    /**
     * Get blog category by ID.
     */
    public function getById($id)
    {
        return BlogCategory::with(['translations', 'attachments'])->findOrFail($id);
    }

    /**
     * Create blog category.
     */
    public function create($data)
    {
        DB::beginTransaction();

        try {
            // Create blog category
            $blogCategory = BlogCategory::create([
                'active' => $data['active'] ?? true,
            ]);

            // Handle image upload
            if (isset($data['image'])) {
                $blogCategory->attachments()->create([
                    'path' => $data['image'],
                    'type' => 'image',
                ]);
            }

            // Store translations
            $this->storeTranslations($blogCategory, $data['translations'] ?? []);

            DB::commit();
            return $blogCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update blog category.
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $blogCategory = BlogCategory::findOrFail($id);

            // Prepare update data
            $updateData = [
                'active' => $data['active'] ?? $blogCategory->active,
            ];

            // Update blog category
            $blogCategory->update($updateData);

            // Handle image upload
            if (isset($data['image'])) {
                // Remove old image if exists
                $blogCategory->attachments()->where('type', 'image')->forceDelete();

                $blogCategory->attachments()->create([
                    'path' => $data['image'],
                    'type' => 'image',
                ]);
            }

            // Store translations
            $this->storeTranslations($blogCategory, $data['translations'] ?? []);

            DB::commit();
            return $blogCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete blog category.
     */
    public function delete($id)
    {
        $blogCategory = BlogCategory::findOrFail($id);

        // Check if category has blogs
        if ($blogCategory->blogs()->count() > 0) {
            throw new \Exception('Cannot delete blog category that has blogs associated with it.');
        }

        $blogCategory->attachments()->where('type', 'image')->forceDelete();

        return $blogCategory->delete();
    }

    /**
     * Store translations for blog category.
     */
    private function storeTranslations($blogCategory, $translations)
    {
        foreach ($translations as $languageId => $translationData) {
            // Get language code
            $language = Language::find($languageId);
            if (!$language) continue;

            foreach ($translationData as $field => $value) {
                // Handle Slug Generation for English Title
                if ($field == 'title' && $language->code == 'en') {
                    $query = BlogCategory::where('slug', \Illuminate\Support\Str::slug($value));
                    
                    // Assuming withoutCountryFilter is a scope on BlogCategory model
                    if (method_exists(BlogCategory::class, 'scopeWithoutCountryFilter')) {
                        $query->withoutCountryFilter();
                    }
                    
                    if ($blogCategory->exists) {
                        $query->where('id', '!=', $blogCategory->id);
                    }
                    
                    $model = $query->first();

                    if($model) {
                        $newSlug = $model->slug . '-' . rand(1, 1000);
                        $blogCategory->update([
                            'slug' => $newSlug
                        ]);
                    } else {
                        $blogCategory->update([
                            'slug' => \Illuminate\Support\Str::slug($value)
                        ]);
                    }
                }

                // Stores translation
                // If value is array (like meta_keywords), implode it to comma-separated string
                if (is_array($value)) {
                    $value = !empty($value) ? implode(',', $value) : null;
                }

                if ($value !== null && trim((string)$value) !== '') {
                    $blogCategory->translations()->updateOrCreate(
                        [
                            'lang_id' => $languageId,
                            'lang_key' => $field,
                        ],
                        [
                            'lang_value' => $value,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Get active blog categories for dropdown.
     */
    public function getActiveForDropdown()
    {
        return BlogCategory::active()
            ->with(['translations' => function($query) {
                $query->where('lang_key', 'title');
            }])
            ->get()
            ->map(function ($category) {
                return (object) [
                    'id' => $category->id,
                    'title' => $category->title,
                ];
            });
    }
}
