<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;

class CategoryApiService
{
    protected $CategoryRepository;

    public function __construct(CategoryApiRepositoryInterface $CategoryRepository)
    {
        $this->CategoryRepository = $CategoryRepository;
    }

    /**
     * Get all activities with filters and pagination
     */
    public function getAllCategories(array $filters = [])
    {
        return $this->CategoryRepository->getAllCategories($filters);
    }

    /**
     * Get Category by ID
     */
    public function find(array $filters = [],$id)
    {
        return $this->CategoryRepository->find($filters, $id);
    }
}
