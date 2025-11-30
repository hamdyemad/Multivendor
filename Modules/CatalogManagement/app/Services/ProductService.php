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

    /**
     * Change vendor product status
     */
    public function changeVendorProductStatus(int $productId, array $data)
    {
        return $this->productInterface->changeVendorProductStatus($productId, $data);
    }

    /**
     * Change product activation status
     */
    public function changeProductActivation(int $productId, bool $isActive)
    {
        return $this->productInterface->changeProductActivation($productId, $isActive);
    }
}
