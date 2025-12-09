<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\AreaSettings\app\Actions\CityQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\CityApiRepositoryInterface;

class CityApiRepository implements CityApiRepositoryInterface
{
    public function __construct(private CityQueryAction $query, private IsPaginatedAction $paginated){}

    public function getAllCities(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $filters["per_page"] ?? null, $paginated);
        return $result;
    }

    public function getCitiesByCountry($id, array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;

        $query = $this->query->handle($filters)->whereHas('country', function($q) use ($id) {
            $q->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->active();
        });

        $result = $this->paginated->handle($query, $filters["per_page"] ?? null, $paginated);
        return $result;
    }
}
