<?php

namespace Modules\AreaSettings\app\Interfaces\Api;

interface CountryApiRepositoryInterface
{
    public function getAllCountries(array $filters = []);
    public function getCountryById($id, array $filters = []);
}
