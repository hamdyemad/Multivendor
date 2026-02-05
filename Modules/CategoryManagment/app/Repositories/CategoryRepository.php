<?php

namespace Modules\CategoryManagment\app\Repositories;

use Modules\CategoryManagment\app\Interfaces\CategoryRepositoryInterface;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Traits\HandlesSortNumber;
use Illuminate\Support\Facades\Storage;

class CategoryRepository implements CategoryRepositoryInterface
{
    use HandlesSortNumber;

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [], int $perPage = 15)
    {
        $query = Category::with('translations', 'department', 'subs')->filter($filters);

        // Order by latest
        $query->orderBy('sort_number', 'asc');
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get categories query for DataTables
     */
    public function getCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        return Category::with(['translations', 'department'])
            ->filter($filters)
            ->sorted($orderBy, $orderDirection, 'sort_number', 'asc');
    }


    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return Category::with('translations')->where('active', 1)->get();
    }

    /**
     * Find category by ID
     */
    public function findById(int $id)
    {
        return Category::with(['translations', 'department'])->findOrFail($id);
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $sortNumber = $data['sort_number'] ?? 1;
            
            // Handle sort number before creating (global scope - no additional conditions)
            $this->handleSortNumber(Category::class, null, $sortNumber);
            
            $category = Category::create([
                'department_id' => $data['department_id'],
                'active' => $data['active'] ?? 1,
                'sort_number' => $sortNumber,
                'view_status' => $data['view_status'] ?? 1,
            ]);

            // Store translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['name'])) {
                        $category->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }

                    if (!empty($translation['description'])) {
                        $category->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'description',
                            'lang_value' => $translation['description'],
                        ]);
                    }
                }
            }


            // Handle image upload
            if (isset($data['image'])) {
                $path = $data['image']->store("categories/$category->id", 'public');
                $category->attachments()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }

            // Handle icon upload
            if (isset($data['icon'])) {
                $path = $data['icon']->store("categories/$category->id", 'public');
                $category->attachments()->create([
                    'path' => $path,
                    'type' => 'icon'
                ]);
            }

            return $category;
        });
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data)
    {
        return \DB::transaction(function () use ($id, $data) {
            $category = Category::findOrFail($id);
            $updatedData = [];
            (isset($data['department_id'])) ? $updatedData['department_id'] = $data['department_id'] : null;
            if(isset($data['active'])) {
                if($data['active'] == 1) {
                    $updatedData['active'] = 1;
                } else {
                    $updatedData['active'] = 0;
                }
            }
            
            // Handle sort_number to prevent duplicates GLOBALLY (across all departments)
            if(isset($data['sort_number'])) {
                $newSortNumber = (int) $data['sort_number'];
                $oldSortNumber = $category->sort_number;
                
                // Use the trait handler function (global scope - no additional conditions)
                $this->handleSortNumber(Category::class, $id, $newSortNumber, $oldSortNumber);
                
                $updatedData['sort_number'] = $newSortNumber;
            }
            
            if(isset($data['view_status'])) {
                $updatedData['view_status'] = $data['view_status'] == 1 ? 1 : 0;
            }
            $category->update($updatedData);

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $category->translations()->forceDelete();

            // Create new translations
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $category->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $category->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            $oldImage = $category->attachments()->where('type', 'image')->first();
            if ($oldImage) {
                if(Storage::disk('public')->exists($oldImage->path)) {
                    Storage::disk('public')->delete($oldImage->path);
                }
                $oldImage->delete();
            }

            // Store new image
            $path = $data['image']->store("categories/{$category->id}", 'public');
            $category->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        // Handle icon upload
        if (isset($data['icon']) && $data['icon']) {
            // Delete old icon if exists
            $oldIcon = $category->attachments()->where('type', 'icon')->first();
            if ($oldIcon) {
                if(Storage::disk('public')->exists($oldIcon->path)) {
                    Storage::disk('public')->delete($oldIcon->path);
                }
                $oldIcon->delete();
            }

            // Store new icon
            $path = $data['icon']->store("categories/{$category->id}", 'public');
            $category->attachments()->create([
                'path' => $path,
                'type' => 'icon'
            ]);
        }

        // Touch the model to trigger GlobalModelObserver for activity logging
        $category->touch();

        return $category;
        });
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id)
    {
        return \DB::transaction(function () use ($id) {
            $category = Category::findOrFail($id);
            $deletedSortNumber = $category->sort_number;
            
            $oldImage = $category->attachments()->where('type', 'image')->first();
            $oldIcon = $category->attachments()->where('type', 'icon')->first();
            if ($oldImage) {
                $oldImage->delete();
            }
            if ($oldIcon) {
                $oldIcon->delete();
            }
            $category->translations()->delete();
            $category->delete();
            
            // Shift down all categories with higher sort numbers to fill the gap (global scope)
            $this->handleSortNumberAfterDelete(Category::class, $deletedSortNumber);
            
            return true;
        });
    }

    /**
     * Get categories by department
     */
    public function getCategoriesByDepartment(int $departmentId)
    {
        return Category::with('translations')
            ->where('department_id', $departmentId)
            ->where('active', 1)
            ->get();
    }
}
