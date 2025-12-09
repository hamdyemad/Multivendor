<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface PromocodeRepositoryInterface
{
    /**
     * Get all promocodes with filters and pagination
     */
    public function getAllPromocodes(int $perPage, array $filters = []);

    /**
     * Get promocodes query for DataTables
     */
    public function getPromocodesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get promocode by ID
     */
    public function getPromocodeById(int $id);

    /**
     * Create a new promocode
     */
    public function createPromocode(array $data);

    /**
     * Update promocode
     */
    public function updatePromocode(int $id, array $data);

    /**
     * Delete promocode
     */
    public function deletePromocode(int $id);
}
