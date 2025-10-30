<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface VariantConfigurationKeyRepositoryInterface
{
    /**
     * Get all variant configuration keys with filters and pagination
     */
    public function getAllVariantConfigurationKeys($filters, $perPage = 10);

    /**
     * Get variant configuration keys query for DataTables
     */
    public function getVariantConfigurationKeysQuery(array $filters = []);

    /**
     * Find variant configuration key by ID
     */
    public function findById(int $id);

    /**
     * Create a new variant configuration key
     */
    public function createVariantConfigurationKey(array $data);

    /**
     * Update variant configuration key
     */
    public function updateVariantConfigurationKey(int $id, array $data);

    /**
     * Delete variant configuration key
     */
    public function deleteVariantConfigurationKey(int $id);

}
