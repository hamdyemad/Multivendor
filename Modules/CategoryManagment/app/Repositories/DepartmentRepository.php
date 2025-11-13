<?php

namespace Modules\CategoryManagment\app\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CategoryManagment\app\Interfaces\DepartmentRepositoryInterface;
use Modules\CategoryManagment\app\Models\Department;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], int $perPage = 15)
    {
        $query = Department::with('translations');

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

        return $query->paginate($perPage);
    }

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Department::with('translations');
        
        Log::info('Department Repository - Query Start', ['filters' => $filters]);
        
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            Log::info('Department Repository - Applying search filter', ['search' => $search]);
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
                // Sort by translation using polymorphic relationship
                Log::info('Department Repository - Applying translation sort', [
                    'lang_id' => $orderBy['lang_id'], 
                    'direction' => $orderDirection
                ]);
                
                $query->join('translations as t', function($join) use ($orderBy) {
                    $join->on('departments.id', '=', 't.translatable_id')
                         ->where('t.translatable_type', '=', 'Modules\CategoryManagment\app\Models\Department')
                         ->where('t.lang_id', '=', $orderBy['lang_id'])
                         ->where('t.lang_key', '=', 'name');
                })
                ->orderBy('t.lang_value', $orderDirection)
                ->select('departments.*');
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
     * Get all active departments
     */
    public function getActiveDepartments()
    {
        return Department::with('translations')->where('active', 1)->get();
    }

    /**
     * Find department by ID
     */
    public function findById(int $id)
    {
        return Department::with('translations')->findOrFail($id);
    }

    /**
     * Create a new department
     */
    public function createDepartment(array $data)
    {
        $department = Department::create([
            'slug' => Str::uuid(),
            'active' => $data['active'] ?? 1,
        ]);

        // Store translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        if(isset($data['activities'])) {
            $department->activities()->sync($data['activities']);
        }

        // Handle image upload
        if (isset($data['image'])) {
            $path = $data['image']->store("departments/$department->id", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        return $department;
    }

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data)
    {
        $department = Department::findOrFail($id);
        
        $updateData = [
            'active' => $data['active'] ?? 1,
        ];

        $department->update($updateData);

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            $oldImage = $department->attachments()->where('type', 'image')->first();
            if ($oldImage) {
                if(Storage::disk('public')->exists($oldImage->path)) {
                    Storage::disk('public')->delete($oldImage->path);
                }
                $oldImage->delete();
            }
            // Store new image
            $path = $data['image']->store("departments/{$department->id}", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $department->translations()->delete();

            // Create new translations
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }
        if(isset($data['activities'])) {
            $department->activities()->sync($data['activities']);
        }

        return $department;
    }

    /**
     * Delete department
     */
    public function deleteDepartment(int $id)
    {
        $department = Department::findOrFail($id);
        $oldImage = $department->attachments()->where('type', 'image')->first();
        if ($oldImage) {
            $oldImage->delete();
        }
        $department->translations()->delete();
        $department->delete();
        return true;
    }
}
