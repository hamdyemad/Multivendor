<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface ProductInterface
{
    public function getAllProducts(array $filters = [], int $perPage = 10);
    public function getProductById($id);
    public function createProduct(array $data);
    public function updateProduct(int $id, array $data);
    public function deleteProduct(int $id);
    public function updateStockAndPricing($id, array $data);
}
