<?php

namespace Modules\SystemSetting\app\Repositories\Api;

use Modules\SystemSetting\app\Interfaces\Api\AdApiRepositoryInterface;
use Modules\SystemSetting\app\Models\Ad;

class AdApiRepository implements AdApiRepositoryInterface
{
    public function all($data = [])
    {
        return Ad::with('translations', 'attachments')->active()->filter($data)->get();
    }

    public function find($id)
    {
        return Ad::with('translations', 'attachments')->findOrFail($id);
    }

}
