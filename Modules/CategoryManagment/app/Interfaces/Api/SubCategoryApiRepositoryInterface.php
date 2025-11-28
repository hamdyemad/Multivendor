<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;

interface SubCategoryApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllSubCategories(CategoryFilterDTO $filters);

    /**
     * Get activity by ID
     */
    public function find(CategoryFilterDTO $filters, $id);

    /**
     * Get sub-categories by category ID or slug
     */
    public function getSubCategoriesByCategory($categoryId);

    /**
     * Get sub-category by ID or slug
     */
    public function getSubCategoryById($subCategoryId);
}
