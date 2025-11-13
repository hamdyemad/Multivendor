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

    public function getQuery(array $filters = [])
    {
        return $this->productInterface->getQuery($filters);
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
}
