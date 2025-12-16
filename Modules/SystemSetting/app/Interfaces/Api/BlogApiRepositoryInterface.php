<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface BlogApiRepositoryInterface
{
    public function all($filters = []);

    public function find($id);
}
