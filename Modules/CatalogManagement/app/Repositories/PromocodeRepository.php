<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\PromocodeRepositoryInterface;
use Modules\CatalogManagement\app\Models\Promocode;

class PromocodeRepository implements PromocodeRepositoryInterface
{
    public function getAllPromocodes(int $perPage = 15, array $filters = [])
    {
        $query = $this->getPromocodesQuery($filters);
        return ($perPage) ? $query->paginate($perPage) : $query->get();
    }

    public function getPromocodesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Promocode::query();

        if (isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null) {
            $query->where('is_active', $filters['active']);
        }

        if (!empty($filters['valid_from'])) {
            $query->whereDate('valid_from', '>=', $filters['valid_from']);
        }

        if (!empty($filters['valid_until'])) {
            $query->whereDate('valid_until', '<=', $filters['valid_until']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['dedicated_to'])) {
            $query->where('dedicated_to', $filters['dedicated_to']);
        }

        if ($orderBy) {
             $query->orderBy($orderBy, $orderDirection);
        } else {
             $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function getPromocodeById(int $id)
    {
        return Promocode::findOrFail($id);
    }

    public function createPromocode(array $data)
    {
        return Promocode::create($data);
    }

    public function updatePromocode(int $id, array $data)
    {
        $promocode = $this->getPromocodeById($id);
        $promocode->update($data);
        return $promocode;
    }

    public function deletePromocode(int $id)
    {
        $promocode = $this->getPromocodeById($id);
        return $promocode->delete();
    }
}
