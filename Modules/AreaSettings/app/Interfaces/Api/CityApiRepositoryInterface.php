<?php

namespace Modules\AreaSettings\app\Interfaces\Api;

interface CityApiRepositoryInterface
{
    public function getAllCities(array $filters = []);
    public function getCitiesByCountry($id, array $filters = []);
}
