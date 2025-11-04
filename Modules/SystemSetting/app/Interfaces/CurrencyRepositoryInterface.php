<?php

namespace Modules\SystemSetting\app\Interfaces;

interface CurrencyRepositoryInterface
{
    /**
     * Get all currencies with filters and pagination
     */
    public function getAllCurrencies(array $filters = [], ?int $perPage = 15);

    /**
     * Get currencies query for DataTables
     */
    public function getCurrenciesQuery(array $filters = []);

    /**
     * Get currency by ID
     */
    public function getCurrencyById(int $id);

    /**
     * Create a new currency
     */
    public function createCurrency(array $data);

    /**
     * Update currency
     */
    public function updateCurrency(int $id, array $data);

    /**
     * Delete currency
     */
    public function deleteCurrency(int $id);

    /**
     * Get active currencies
     */
    public function getActiveCurrencies();
}
