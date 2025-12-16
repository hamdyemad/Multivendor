<?php

namespace Modules\SystemSetting\app\Services\Api;

use Modules\SystemSetting\app\Interfaces\Api\BlogCategoryApiRepositoryInterface;

class BlogCategoryApiService
{
    protected $blogCategoryRepository;

    public function __construct(BlogCategoryApiRepositoryInterface $blogCategoryRepository)
    {
        $this->blogCategoryRepository = $blogCategoryRepository;
    }

    public function getAll($filters = [])
    {
        return $this->blogCategoryRepository->all($filters);
    }

    public function find($id)
    {
        return $this->blogCategoryRepository->find($id);
    }
}
