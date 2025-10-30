<?php

namespace Modules\CategoryManagment\app\Repositories;

use Modules\CategoryManagment\app\Interfaces\SubCategoryRepositoryInterface;
use Modules\CategoryManagment\app\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    /**
     * Get all sub-categories with filters and pagination
     */
    public function getAllSubCategories(array $filters = [], int $perPage = 15)
    {
        $query = SubCategory::with(['translations', 'category']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // If perPage is 0, return all results without pagination (for dropdowns)
        if ($perPage === 0) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get sub-categories query for DataTables
     */
    public function getSubCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = SubCategory::with(['translations', 'category']);
        
        \Log::info('SubCategory Repository - Query Start', ['filters' => $filters]);
        
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            \Log::info('SubCategory Repository - Applying search filter', ['search' => $search]);
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy) {
            if (is_array($orderBy) && isset($orderBy['lang_id'])) {
                // Sort by translation
                $query->join('translations as sct', 'sub_categories.id', '=', 'sct.translatable_id')
                    ->where('sct.translatable_type', 'Modules\\CategoryManagment\\app\\Models\\SubCategory')
                    ->where('sct.lang_id', $orderBy['lang_id'])
                    ->where('sct.lang_key', 'name')
                    ->orderBy('sct.lang_value', $orderDirection)
                    ->select('sub_categories.*');
            } else {
                // Sort by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Get all active sub-categories
     */
    public function getActiveSubCategories()
    {
        return SubCategory::with('translations')->where('active', 1)->get();
    }

    /**
     * Find sub-category by ID
     */
    public function findById(int $id)
    {
        return SubCategory::with(['translations', 'category.translations', 'category.department.translations'])->findOrFail($id);
    }

    /**
     * Create a new sub-category
     */
    public function createSubCategory(array $data)
    {
        $subCategory = SubCategory::create([
            'slug' => Str::uuid(),
            'category_id' => $data['category_id'],
            'active' => $data['active'] ?? 1,
        ]);

        // Store translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $subCategory->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $subCategory->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        // Handle image upload
        if (isset($data['image'])) {
            $path = $data['image']->store("subcategories/{$subCategory->id}", 'public');
            $subCategory->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        return $subCategory;
    }

    /**
     * Update sub-category
     */
    public function updateSubCategory(int $id, array $data)
    {
        $subCategory = SubCategory::findOrFail($id);
        
        $subCategory->update([
            'category_id' => $data['category_id'],
            'active' => $data['active'] ?? 1,
        ]);

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $subCategory->translations()->delete();

            // Create new translations
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $subCategory->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $subCategory->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image if exists
            $oldImage = $subCategory->attachments()->where('type', 'image')->first();
            if ($oldImage) {
                if(Storage::disk('public')->exists($oldImage->path)) {
                    Storage::disk('public')->delete($oldImage->path);
                }
                $oldImage->delete();
            }
            
            // Store new image
            $path = $data['image']->store("subcategories/{$subCategory->id}", 'public');
            $subCategory->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        return $subCategory;
    }

    /**
     * Delete sub-category
     */
    public function deleteSubCategory(int $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->translations()->delete();
        $subCategory->delete();
        return true;
    }

    /**
     * Get sub-categories by category
     */
    public function getSubCategoriesByCategory(int $categoryId)
    {
        return SubCategory::with('translations')
            ->where('category_id', $categoryId)
            ->where('active', 1)
            ->get();
    }
}
