<?php

namespace Modules\AreaSettings\app\Services\Api;

use Modules\AreaSettings\app\Interfaces\Api\CountryApiRepositoryInterface;

class CountryApiService
{
    private $countryRepository;

    public function __construct(CountryApiRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Get all countries with filters and pagination
     */
    public function getAll(array $filters = [])
    {
        return $this->countryRepository->getAllCountries($filters);
    }

    public function getCountryById($id, array $filters = [])
    {
        return $this->countryRepository->getCountryById($id, $filters);
    }
}
