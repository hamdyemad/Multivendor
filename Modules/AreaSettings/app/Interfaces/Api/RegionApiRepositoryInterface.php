<?php

namespace Modules\AreaSettings\app\Interfaces\Api;

interface RegionApiRepositoryInterface
{
    public function getAllRegions(array $filters = []);
    public function getRegionsByCity($id, array $filters = []);
}
