<?php

namespace Modules\CategoryManagment\app\Repositories;

use Modules\CategoryManagment\app\Interfaces\CategoryRepositoryInterface;
use Modules\CategoryManagment\app\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [], int $perPage = 15)
    {
        $query = Category::with('translations', 'department');
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Department Filter
        if (isset($filters['department_id']) && $filters['department_id'] !== '') {
            $query->where('department_id', $filters['department_id']);
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
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
     * Get categories query for DataTables
     */
    public function getCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Category::query();
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
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

        // Department filter
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
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
                // Sort by translation using subquery
                $langId = $orderBy['lang_id'];
                $query->orderByRaw("(
                    SELECT lang_value 
                    FROM translations 
                    WHERE translations.translatable_id = categories.id 
                    AND translations.translatable_type = 'Modules\\\\CategoryManagment\\\\app\\\\Models\\\\Category' 
                    AND translations.lang_id = ? 
                    AND translations.lang_key = 'name'
                    LIMIT 1
                ) {$orderDirection}", [$langId]);
            } elseif ($orderBy === 'department') {
                // Sort by department name using subquery
                $currentLocale = app()->getLocale();
                $langId = $currentLocale === 'ar' ? 1 : 2;
                
                $query->orderByRaw("(
                    SELECT dt.lang_value 
                    FROM departments d
                    LEFT JOIN translations dt ON d.id = dt.translatable_id 
                        AND dt.translatable_type = 'Modules\\\\CategoryManagment\\\\app\\\\Models\\\\Department'
                        AND dt.lang_key = 'name'
                        AND dt.lang_id = ?
                    WHERE d.id = categories.department_id
                    LIMIT 1
                ) {$orderDirection}", [$langId]);
            } else {
                // Sort by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Load relationships after sorting is applied
        $query->with(['translations', 'department']);

        return $query;
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
        $category = Category::create([
            'slug' => \Str::uuid(),
            'department_id' => $data['department_id'],
            'active' => $data['active'] ?? 1,
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

        return $category;
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data)
    {
        $category = Category::findOrFail($id);
        
        $category->update([
            'department_id' => $data['department_id'],
            'active' => $data['active'] ?? 1,
        ]);

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $category->translations()->delete();

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

        return $category;
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id)
    {
        $category = Category::findOrFail($id);
        $category->translations()->delete();
        $category->delete();
        return true;
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
