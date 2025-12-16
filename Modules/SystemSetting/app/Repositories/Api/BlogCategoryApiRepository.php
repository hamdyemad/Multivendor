<?php

namespace Modules\SystemSetting\app\Repositories\Api;

use Modules\SystemSetting\app\Interfaces\Api\BlogCategoryApiRepositoryInterface;
use Modules\SystemSetting\app\Models\BlogCategory;

class BlogCategoryApiRepository implements BlogCategoryApiRepositoryInterface
{
    public function all($filters = [])
    {
        $query = BlogCategory::with(['translations', 'attachments'])->active()
        ->filter($filters)->latest();
        if(isset($filters['per_page'])) {
            return ($filters['per_page'] == 0) ? $query->get() : $query->paginate($filters['per_page']);
        } else {
            return $query->paginate(10);
        }
    }

    public function find($id)
    {
        return BlogCategory::with(['translations', 'attachments'])->active()
        ->where('slug', $id)
        ->orWhere('id', $id)
        ->firstOrFail();
    }
}
