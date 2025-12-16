<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\BlogRepositoryInterface;
use Modules\SystemSetting\app\Models\Blog;

class BlogService
{
    protected $blogRepository;

    public function __construct(BlogRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * Get all blogs.
     */
    public function getAll($filters = [])
    {
        return $this->blogRepository->getAll($filters);
    }

    public function getAllBlogs()
    {
        return $this->blogRepository->getAll();
    }

    /**
     * Get blog by ID.
     */
    public function getBlogById($id)
    {
        return $this->blogRepository->getById($id);
    }

    /**
     * Create blog.
     */
    public function createBlog($data)
    {
        return $this->blogRepository->create($data);
    }

    /**
     * Update blog.
     */
    public function updateBlog($id, $data)
    {
        return $this->blogRepository->update($id, $data);
    }

    /**
     * Delete blog.
     */
    public function deleteBlog($id)
    {
        return $this->blogRepository->delete($id);
    }

    /**
     * Get active blogs for frontend.
     */
    public function getActiveBlogsForFrontend($limit = null)
    {
        return $this->blogRepository->getActiveForFrontend($limit);
    }

    /**
     * Get blogs by category.
     */
    public function getBlogsByCategory($categoryId, $limit = null)
    {
        return $this->blogRepository->getByCategory($categoryId, $limit);
    }
}
