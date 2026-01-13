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

        // View status filter
        if (isset($filters['view_status']) && $filters['view_status'] !== '') {
            $query->where('view_status', $filters['view_status']);
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
    public function getSubCategoriesQuery(array $filters = [])
    {
        $orderBy = $filters['orderBy'] ?? null;
        $sortBy = $filters['sortBy'] ?? 'asc';

        return SubCategory::with(['translations', 'category'])
            ->filter($filters)
            ->sorted($orderBy, $sortBy, 'sort_number', 'asc');
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
            'category_id' => $data['category_id'],
            'active' => $data['active'] ?? 1,
            'sort_number' => $data['sort_number'] ?? 0,
            'view_status' => $data['view_status'] ?? 1,
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

        // Handle icon upload
        if (isset($data['icon'])) {
            $path = $data['icon']->store("subcategories/{$subCategory->id}", 'public');
            $subCategory->attachments()->create([
                'path' => $path,
                'type' => 'icon'
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
        $updatedData = [];
        (isset($data['category_id'])) ? $updatedData['category_id'] = $data['category_id'] : null;
        if(isset($data['active'])) {
            $updatedData['active'] = $data['active'] == 1 ? 1 : 0;
        }
        if(isset($data['sort_number'])) {
            $updatedData['sort_number'] = (int) $data['sort_number'];
        }
        if(isset($data['view_status'])) {
            $updatedData['view_status'] = $data['view_status'] == 1 ? 1 : 0;
        }
        $subCategory->update($updatedData);

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $subCategory->translations()->forceDelete();

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

        // Handle icon upload
        if (isset($data['icon']) && $data['icon'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old icon if exists
            $oldIcon = $subCategory->attachments()->where('type', 'icon')->first();
            if ($oldIcon) {
                if(Storage::disk('public')->exists($oldIcon->path)) {
                    Storage::disk('public')->delete($oldIcon->path);
                }
                $oldIcon->delete();
            }

            // Store new icon
            $path = $data['icon']->store("subcategories/{$subCategory->id}", 'public');
            $subCategory->attachments()->create([
                'path' => $path,
                'type' => 'icon'
            ]);
        }

        // Touch the model to trigger GlobalModelObserver for activity logging
        $subCategory->touch();

        return $subCategory;
    }

    /**
     * Delete sub-category
     */
    public function deleteSubCategory(int $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $oldImage = $subCategory->attachments()->where('type', 'image')->first();
        $oldIcon = $subCategory->attachments()->where('type', 'icon')->first();
        if ($oldImage) {
            $oldImage->delete();
        }
        if ($oldIcon) {
            $oldIcon->delete();
        }
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
