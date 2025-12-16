<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\BlogCategoryRepositoryInterface;
use Modules\SystemSetting\app\Models\BlogCategory;

class BlogCategoryService
{
    protected $blogCategoryRepository;

    public function __construct(BlogCategoryRepositoryInterface $blogCategoryRepository)
    {
        $this->blogCategoryRepository = $blogCategoryRepository;
    }

    /**
     * Get all blog categories.
     */
    public function getAll($filters = [])
    {
        return $this->blogCategoryRepository->getAll($filters);
    }

    public function getAllBlogCategories()
    {
        return $this->blogCategoryRepository->getAll();
    }

    /**
     * Get blog category by ID.
     */
    public function getBlogCategoryById($id)
    {
        return $this->blogCategoryRepository->getById($id);
    }

    /**
     * Create blog category.
     */
    public function createBlogCategory($data)
    {
        return $this->blogCategoryRepository->create($data);
    }

    /**
     * Update blog category.
     */
    public function updateBlogCategory($id, $data)
    {
        return $this->blogCategoryRepository->update($id, $data);
    }

    /**
     * Delete blog category.
     */
    public function deleteBlogCategory($id)
    {
        return $this->blogCategoryRepository->delete($id);
    }

    /**
     * Get active blog categories for dropdown.
     */
    public function getActiveBlogCategoriesForDropdown()
    {
        return $this->blogCategoryRepository->getActiveForDropdown();
    }
}
