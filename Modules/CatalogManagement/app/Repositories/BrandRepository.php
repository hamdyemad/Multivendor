<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\BrandRepositoryInterface;
use Modules\CatalogManagement\app\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * Get all brands with filters and pagination
     */
    public function getAllBrands(int $perPage = 15, array $filters = [])
    {
        $query = Brand::with('translations', 'logo', 'cover');

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

        return ($perPage) ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get brands query for DataTables
     */
    public function getBrandsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Brand::with('translations', 'logo', 'cover');

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

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name or description
                $langId = $orderBy['lang_id'];
                $langKey = $orderBy['key'] ?? 'name';
                $query->leftJoin('translations as t_sort', function($join) use ($langId, $langKey) {
                    $join->on('brands.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'Modules\\Brands\\app\\Models\\Brand')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', $langKey);
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('brands.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get brands query for Select2 AJAX (with search support)
     */
    public function getAllBrandsQuery(array $filters = [])
    {
        $query = Brand::with('translations', 'logo')->where('active', 1);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%")
                          ->where('lang_key', 'name');
                });
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Get brand by ID
     */
    public function getBrandById(int $id)
    {
        return Brand::with('translations', 'logo', 'cover')->findOrFail($id);
    }

    /**
     * Create a new brand
     */
    public function createBrand(array $data)
    {
        return DB::transaction(function () use ($data) {
            $brand = Brand::create([
                'slug' => Str::uuid(),
                'active' => $data['active'] ?? 0,
                'facebook_url' => $data['facebook_url'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'pinterest_url' => $data['pinterest_url'] ?? null,
                'twitter_url' => $data['twitter_url'] ?? null,
                'instagram_url' => $data['instagram_url'] ?? null,
            ]);

            // Handle Logo
            if(isset($data['logo']) && $data['logo']) {
                $path = $data['logo']->store("brands/$brand->id", 'public');

                $attachment = $brand->attachments()->create([
                    'path' => $path,
                    'type' => 'logo',
                ]);
            }

            // Handle Cover
            if(isset($data['cover'])) {
                $path = $data['cover']->store("brands/$brand->id", 'public');
                $brand->attachments()->create([
                    'path' => $path,
                    'type' => 'cover',
                ]);
            }

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $brand->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                    if (isset($translation['description'])) {
                        $brand->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'description',
                            'lang_value' => $translation['description'],
                        ]);
                    }
                }
            }

            $brand->refresh();
            $brand->load('translations', 'logo', 'cover');

            return $brand;
        });
    }

    /**
     * Update brand
     */
    public function updateBrand(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $brand = Brand::findOrFail($id);

            $brand->update([
                'active' => $data['active'] ?? 0,
                'facebook_url' => $data['facebook_url'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'pinterest_url' => $data['pinterest_url'] ?? null,
                'twitter_url' => $data['twitter_url'] ?? null,
                'instagram_url' => $data['instagram_url'] ?? null,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $brand->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                    if (isset($translation['description'])) {
                        $brand->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'description',
                            ],
                            [
                                'lang_value' => $translation['description'],
                            ]
                        );
                    }
                }
            }

            // Handle Logo
            if(isset($data['logo'])) {
                // Delete old logo if exists
                $oldLogo = $brand->logo;
                if ($oldLogo) {
                    Storage::disk('public')->delete($oldLogo->path);
                    $oldLogo->delete();
                }

                // Store new logo
                $path = $data['logo']->store("brands/$brand->id", 'public');
                $brand->attachments()->create([
                    'path' => $path,
                    'type' => 'logo',
                ]);
            }

            // Handle Cover
            if(isset($data['cover'])) {
                // Delete old cover if exists
                $oldCover = $brand->cover;
                if ($oldCover) {
                    Storage::disk('public')->delete($oldCover->path);
                    $oldCover->delete();
                }

                // Store new cover
                $path = $data['cover']->store("brands/$brand->id", 'public');
                $brand->attachments()->create([
                    'path' => $path,
                    'type' => 'cover',
                ]);
            }

            $brand->refresh();
            $brand->load('translations', 'logo', 'cover');

            return $brand;
        });
    }

    /**
     * Delete brand
     */
    public function deleteBrand(int $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->translations()->delete();

        // Delete attachments
        if($brand->attachments) {
            foreach ($brand->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
            }
            $brand->attachments()->delete();
        }
        return $brand->delete();
    }

    /**
     * Get active brands
     */
    public function getActiveBrands()
    {
        return Brand::with('translations', 'logo')->where('active', 1)
            ->get();
    }
}
