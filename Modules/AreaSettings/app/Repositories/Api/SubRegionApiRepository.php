<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\AreaSettings\app\Actions\SubRegionQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\SubRegionApiRepositoryInterface;

class SubRegionApiRepository implements SubRegionApiRepositoryInterface
{
    public function __construct(private SubRegionQueryAction $query, private IsPaginatedAction $paginated){}

    public function getAllSubRegions(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    public function getSubRegionsByRegions(array $filters = [], $id)
    {
        $paginated = isset($filters["paginated"]) ? true : false;

        $query = $this->query->handle($filters)->whereHas('region', function($q) use ($id) {
            $q->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->active();
        });

        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }
}
