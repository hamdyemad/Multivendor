<?php

namespace Modules\AreaSettings\app\Services\Api;

use Modules\AreaSettings\app\Interfaces\Api\RegionApiRepositoryInterface;

class RegionApiService
{
    private $RegionRepository;

    public function __construct(RegionApiRepositoryInterface $RegionRepository)
    {
        $this->RegionRepository = $RegionRepository;
    }

    /**
     * Get all countries with filters and pagination
     */
    public function getAll(array $filters = [])
    {
        return $this->RegionRepository->getAllRegions($filters);
    }

    public function getRegionsByCity($id, array $filters = [])
    {
        return $this->RegionRepository->getRegionsByCity($id, $filters);
    }
}
