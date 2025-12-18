<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface AdApiRepositoryInterface
{
    public function all($data = []);

    public function find($id);
}
