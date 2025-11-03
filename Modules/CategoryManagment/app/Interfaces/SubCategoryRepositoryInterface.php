<?php

namespace Modules\CategoryManagment\app\Interfaces;

interface SubCategoryRepositoryInterface
{
    /**
     * Get all sub-categories with filters and pagination
     */
    public function getAllSubCategories(array $filters = [], int $perPage = 15);

    /**
     * Get sub-categories query for DataTables
     */
    public function getSubCategoriesQuery(array $filters = []);

    /**
     * Get all active sub-categories
     */
    public function getActiveSubCategories();

    /**
     * Find sub-category by ID
     */
    public function findById(int $id);

    /**
     * Create a new sub-category
     */
    public function createSubCategory(array $data);

    /**
     * Update sub-category
     */
    public function updateSubCategory(int $id, array $data);

    /**
     * Delete sub-category
     */
    public function deleteSubCategory(int $id);

    /**
     * Get sub-categories by category
     */
    public function getSubCategoriesByCategory(int $categoryId);
}
