<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\BrandRepositoryInterface;
use Illuminate\Support\Facades\Log;

class BrandService
{
    protected $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Get all brands with filters and pagination
     */
    public function getAllBrands(array $filters = [], int $perPage = 10)
    {
        try {
            return $this->brandRepository->getAllBrands($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching brands: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get brands query for DataTables
     */
    public function getBrandsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->brandRepository->getBrandsQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching brands query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get brands query for Select2 AJAX search
     */
    public function getAllBrandsQuery(array $filters = [])
    {
        try {
            return $this->brandRepository->getAllBrandsQuery($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching brands query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search brands for Select2 (AJAX with pagination)
     */
    public function searchForSelect2($search = '', $page = 1, $perPage = 30)
    {
        try {
            // Build filters for active brands with search
            $filters = [
                'search' => $search,
                'active' => 1
            ];
            
            // Get query from repository
            $query = $this->brandRepository->getAllBrandsQuery($filters);
            
            // Count total for pagination
            $total = $query->count();
            
            // Get paginated brands
            $brands = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Format results for Select2
            $results = $brands->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'text' => $brand->getTranslation('name', app()->getLocale())
                ];
            });

            return [
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error searching brands for Select2: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get brand by ID
     */
    public function getBrandById(int $id)
    {
        try {
            return $this->brandRepository->getBrandById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching brand: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new brand with translations
     */
    public function createBrand(array $data)
    {
        try {
            return $this->brandRepository->createBrand($data);
        } catch (\Exception $e) {
            Log::error('Error creating brand: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update brand with translations
     */
    public function updateBrand(int $id, array $data)
    {
        try {
            return $this->brandRepository->updateBrand($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating brand: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete brand
     */
    public function deleteBrand(int $id)
    {
        try {
            return $this->brandRepository->deleteBrand($id);
        } catch (\Exception $e) {
            Log::error('Error deleting brand: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active brands
     */
    public function getActiveBrands()
    {
        try {
            return $this->brandRepository->getActiveBrands();
        } catch (\Exception $e) {
            Log::error('Error fetching active brands: ' . $e->getMessage());
            throw $e;
        }
    }
}
