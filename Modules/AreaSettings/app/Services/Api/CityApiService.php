<?php

namespace Modules\AreaSettings\app\Services\Api;

use Modules\AreaSettings\app\Interfaces\Api\CityApiRepositoryInterface;

class CityApiService
{
    private $CityRepository;

    public function __construct(CityApiRepositoryInterface $CityRepository)
    {
        $this->CityRepository = $CityRepository;
    }

    /**
     * Get all countries with filters and pagination
     */
    public function getAll(array $filters = [])
    {
        return $this->CityRepository->getAllCities($filters);
    }

    public function getCitiesByCountry($id, array $filters = [])
    {
        return $this->CityRepository->getCitiesByCountry($id, $filters);
    }
}
