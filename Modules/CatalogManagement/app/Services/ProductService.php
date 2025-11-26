<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\ProductInterface;

class ProductService
{
    public function __construct(public ProductInterface $productInterface)
    {
    }

    public function getAllProducts(array $filters = [], int $perPage = 10)
    {
        return $this->productInterface->getAllProducts($filters, $perPage);
    }


    public function getProductById($id)
    {
        return $this->productInterface->getProductById($id);
    }

    public function createProduct(array $data)
    {
        return $this->productInterface->createProduct($data);
    }

    public function updateProduct(int $id, array $data)
    {
        return $this->productInterface->updateProduct($id, $data);
    }

    public function deleteProduct(int $id)
    {
        return $this->productInterface->deleteProduct($id);
    }

    public function updateStockAndPricing($id, array $data)
    {
        return $this->productInterface->updateStockAndPricing($id, $data);
    }

    // Bank Stock Management
    public function searchBankProducts(string $search = '', int $perPage = 20)
    {
        return $this->productInterface->searchBankProducts($search, $perPage);
    }

    public function getVendorProductByProductAndVendor(int $productId, int $vendorId)
    {
        return $this->productInterface->getVendorProductByProductAndVendor($productId, $vendorId);
    }

    public function getProductsNotInVendor(int $vendorId, string $search = '')
    {
        return $this->productInterface->getProductsNotInVendor($vendorId, $search);
    }

    public function saveBankStock(array $data)
    {
        return $this->productInterface->saveBankStock($data);
    }
}
