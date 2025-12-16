<?php

namespace Modules\SystemSetting\app\Services\Api;

use Modules\SystemSetting\app\Interfaces\Api\BlogApiRepositoryInterface;

class BlogApiService
{
    protected $blogRepository;

    public function __construct(BlogApiRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function getAll($filters = [])
    {
        return $this->blogRepository->all($filters);
    }

    public function find($id)
    {
        return $this->blogRepository->find($id);
    }
}
