<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\CurrencyRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Get all currencies with filters and pagination
     */
    public function getAllCurrencies(array $filters = [], ?int $perPage = 15)
    {
        try {
            return $this->currencyRepository->getAllCurrencies($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching currencies: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get currencies query for DataTables
     */
    public function getCurrenciesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->currencyRepository->getCurrenciesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching currencies query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get currency by ID
     */
    public function getCurrencyById(int $id)
    {
        try {
            return $this->currencyRepository->getCurrencyById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching currency: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new currency
     */
    public function createCurrency(array $data)
    {
        try {
            return $this->currencyRepository->createCurrency($data);
        } catch (\Exception $e) {
            Log::error('Error creating currency: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update currency
     */
    public function updateCurrency(int $id, array $data)
    {
        try {
            return $this->currencyRepository->updateCurrency($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating currency: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete currency
     */
    public function deleteCurrency(int $id)
    {
        try {
            return $this->currencyRepository->deleteCurrency($id);
        } catch (\Exception $e) {
            Log::error('Error deleting currency: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active currencies
     */
    public function getActiveCurrencies()
    {
        try {
            return $this->currencyRepository->getActiveCurrencies();
        } catch (\Exception $e) {
            Log::error('Error fetching active currencies: ' . $e->getMessage());
            throw $e;
        }
    }
}
