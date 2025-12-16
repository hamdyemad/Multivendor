<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\Blog;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

use Modules\SystemSetting\app\Interfaces\BlogRepositoryInterface;

class BlogRepository implements BlogRepositoryInterface
{
    /**
     * Get all blogs.
     */
    public function getAll($filters = [])
    {
        return Blog::with(['blogCategory.translations', 'translations', 'attachments'])->filter($filters)->get();
    }

    /**
     * Get blogs for datatable.
     */
    public function getDatatable($request)
    {
        $query = Blog::with(['blogCategory.translations', 'translations', 'attachments'])
        ->filter($request->all());
        return $query;
    }

    /**
     * Get blog by ID.
     */
    public function getById($id)
    {
        return Blog::with(['blogCategory.translations', 'translations', 'attachments'])->findOrFail($id);
    }

    /**
     * Create blog.
     */
    public function create($data)
    {
        DB::beginTransaction();

        try {
            // Create blog
            $blog = Blog::create([
                'blog_category_id' => $data['blog_category_id'],
                'active' => $data['active'] ?? true,
            ]);

            // Handle image upload
            if (isset($data['image'])) {
                $blog->attachments()->create([
                    'path' => $data['image'],
                    'type' => 'image',
                ]);
            }

            // Store translations
            $this->storeTranslations($blog, $data['translations'] ?? []);

            DB::commit();
            return $blog;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update blog.
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $blog = Blog::findOrFail($id);

            // Prepare update data
            $updateData = [
                'blog_category_id' => $data['blog_category_id'] ?? $blog->blog_category_id,
                'active' => $data['active'] ?? $blog->active,
            ];

            // Update blog
            $blog->update($updateData);

            // Handle image upload
            if (isset($data['image'])) {
                // Remove old image if exists
                $blog->attachments()->where('type', 'image')->forceDelete();

                $blog->attachments()->create([
                    'path' => $data['image'],
                    'type' => 'image',
                ]);
            }

            // Store translations
            $this->storeTranslations($blog, $data['translations'] ?? []);

            DB::commit();
            return $blog;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete blog.
     */
    public function delete($id)
    {
        $blog = Blog::findOrFail($id);
        return $blog->delete();
    }

    /**
     * Store translations for blog.
     */
    private function storeTranslations($blog, $translations)
    {
        foreach ($translations as $languageId => $translationData) {
            // Get language code
            $language = Language::find($languageId);
            if (!$language) continue;

            foreach ($translationData as $field => $value) {
                // Handle Slug Generation for English Title
                if ($field == 'title' && $language->code == 'en') {
                    $query = Blog::where('slug', \Illuminate\Support\Str::slug($value));
                    
                    if (method_exists(Blog::class, 'scopeWithoutCountryFilter')) {
                        $query->withoutCountryFilter();
                    }
                    
                    if ($blog->exists) {
                        $query->where('id', '!=', $blog->id);
                    }
                    
                    $model = $query->first();

                    if($model) {
                        $newSlug = $model->slug . '-' . rand(1, 1000);
                        $blog->update([
                            'slug' => $newSlug
                        ]);
                    } else {
                        $blog->update([
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
                    $blog->translations()->updateOrCreate(
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
     * Get active blogs for frontend.
     */
    public function getActiveForFrontend($limit = null)
    {
        $query = Blog::active()
            ->with(['blogCategory.translations', 'translations', 'attachments'])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get blogs by category.
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $query = Blog::active()
            ->byCategory($categoryId)
            ->with(['blogCategory.translations', 'translations', 'attachments'])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
