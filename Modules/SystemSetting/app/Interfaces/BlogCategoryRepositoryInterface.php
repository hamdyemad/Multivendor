<?php

namespace Modules\SystemSetting\app\Interfaces;

interface BlogCategoryRepositoryInterface
{
    public function getAll($filters = []);
    public function getDatatable($request);
    public function getById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getActiveForDropdown();
}
