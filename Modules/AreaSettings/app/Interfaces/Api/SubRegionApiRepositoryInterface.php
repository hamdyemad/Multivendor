<?php

namespace Modules\AreaSettings\app\Interfaces\Api;

interface SubRegionApiRepositoryInterface
{
    public function getAllSubRegions(array $filters = []);
    public function getSubRegionsByRegions($id, array $filters = []);
}
