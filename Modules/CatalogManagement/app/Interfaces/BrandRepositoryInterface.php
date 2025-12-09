<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface BrandRepositoryInterface
{
    /**
     * Get all brands with filters and pagination
     */
    public function getAllBrands(int $perPage, array $filters = []);

    /**
     * Get brands query for DataTables
     */
    public function getBrandsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get brands query for Select2 AJAX
     */
    public function getAllBrandsQuery(array $filters = []);

    /**
     * Get brand by ID
     */
    public function getBrandById(int $id);

    /**
     * Create a new brand
     */
    public function createBrand(array $data);

    /**
     * Update brand
     */
    public function updateBrand(int $id, array $data);

    /**
     * Delete brand
     */
    public function deleteBrand(int $id);

    /**
     * Get active brands
     */
    public function getActiveBrands();
}
