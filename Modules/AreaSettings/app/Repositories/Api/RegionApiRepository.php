<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\AreaSettings\app\Actions\RegionQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\RegionApiRepositoryInterface;

class RegionApiRepository implements RegionApiRepositoryInterface
{
    public function __construct(private RegionQueryAction $query, private IsPaginatedAction $paginated){}

    public function getAllRegions(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $filters["per_page"] ?? null, $paginated);
        return $result;
    }

    public function getRegionsByCity($id, array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;

        $query = $this->query->handle($filters)->whereHas('city', function($q) use ($id) {
            $q->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->active();
        });

        $result = $this->paginated->handle($query, $filters["per_page"] ?? null, $paginated);
        return $result;
    }
}
