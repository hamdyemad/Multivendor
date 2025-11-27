<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\ProductInterface;

class BankService
{
    public function __construct(
        protected ProductInterface $productInterface
    ) {}

    /**
     * Get all bank products with filters
     */
    public function getAllBankProducts(array $filters = [], int $perPage = 20)
    {
        return $this->productInterface->getAllBankProducts($filters, $perPage);
    }

    /**
     * Search bank products
     */
    public function searchBankProducts(string $search = '', ?int $vendorId = null, int $perPage = 20)
    {
        return $this->productInterface->searchBankProducts($search, $vendorId, $perPage);
    }

    /**
     * Get vendor product by product and vendor combination
     */
    public function getVendorProductByProductAndVendor(int $productId, int $vendorId)
    {
        return $this->productInterface->getVendorProductByProductAndVendor($productId, $vendorId);
    }

    /**
     * Get products not in vendor's catalog
     */
    public function getProductsNotInVendor(int $vendorId, string $search = '')
    {
        return $this->productInterface->getProductsNotInVendor($vendorId, $search);
    }

    /**
     * Save bank product stock
     */
    public function saveBankStock(array $data)
    {
        return $this->productInterface->saveBankStock($data);
    }
}
