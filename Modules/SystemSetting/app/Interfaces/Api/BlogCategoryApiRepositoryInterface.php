<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface BlogCategoryApiRepositoryInterface
{
    public function all($filters = []);

    public function find($id);
}
