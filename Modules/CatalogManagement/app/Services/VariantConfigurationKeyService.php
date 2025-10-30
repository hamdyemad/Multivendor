<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\VariantConfigurationKeyRepositoryInterface;
use Illuminate\Support\Facades\Log;

class VariantConfigurationKeyService
{
    protected $variantKeyRepository;

    public function __construct(VariantConfigurationKeyRepositoryInterface $variantKeyRepository)
    {
        $this->variantKeyRepository = $variantKeyRepository;
    }

    /**
     * Get all variant configuration keys with filters and pagination
     */
    public function getAllVariantConfigurationKeys(array $filters = [], int $perPage = 10)
    {
        try {
            return $this->variantKeyRepository->getAllVariantConfigurationKeys($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching variant configuration keys: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get variant configuration keys query for DataTables
     */
    public function getVariantConfigurationKeysQuery(array $filters = [])
    {
        try {
            return $this->variantKeyRepository->getVariantConfigurationKeysQuery($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching variant configuration keys query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get variant configuration key by ID
     */
    public function getVariantConfigurationKeyById(int $id)
    {
        try {
            return $this->variantKeyRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching variant configuration key: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new variant configuration key with translations
     */
    public function createVariantConfigurationKey(array $data)
    {
        try {
            return $this->variantKeyRepository->createVariantConfigurationKey($data);
        } catch (\Exception $e) {
            Log::error('Error creating variant configuration key: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update variant configuration key with translations
     */
    public function updateVariantConfigurationKey(int $id, array $data)
    {
        try {
            return $this->variantKeyRepository->updateVariantConfigurationKey($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating variant configuration key: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete variant configuration key
     */
    public function deleteVariantConfigurationKey(int $id)
    {
        try {
            return $this->variantKeyRepository->deleteVariantConfigurationKey($id);
        } catch (\Exception $e) {
            Log::error('Error deleting variant configuration key: ' . $e->getMessage());
            throw $e;
        }
    }

}
