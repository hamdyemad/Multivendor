<?php

namespace Modules\SystemSetting\app\Interfaces;

interface AdRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function filter(array $filters);
    
    public function toggleStatus($id, $status);
}
