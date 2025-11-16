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
        $query = Department::with('translations')->filter($filters);
        $query->orderBy('created_at', 'desc');
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        // Ensure we exclude soft-deleted records
        $query = Department::with('translations')->filter($filters);

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
