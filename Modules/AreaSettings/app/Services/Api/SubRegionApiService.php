<?php

namespace Modules\AreaSettings\app\Services\Api;

use Modules\AreaSettings\app\Interfaces\Api\SubRegionApiRepositoryInterface;

class SubRegionApiService
{
    private $RegionRepository;

    public function __construct(SubRegionApiRepositoryInterface $RegionRepository)
    {
        $this->RegionRepository = $RegionRepository;
    }

    /**
     * Get all countries with filters and pagination
     */
    public function getAll(array $filters = [])
    {
        return $this->RegionRepository->getAllSubRegions($filters);
    }

    public function getSubRegionsByRegions($id, array $filters = [])
    {
        return $this->RegionRepository->getSubRegionsByRegions($id, $filters);
    }
}
