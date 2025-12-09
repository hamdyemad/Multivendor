<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;
use Illuminate\Support\Facades\Log;

class TaxService
{
    protected $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    /**
     * Get all taxes with filters and pagination
     */
    public function getAllTaxes(int $perPage, array $filters = [])
    {
        try {
            return $this->taxRepository->getAllTaxes($perPage, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching taxes: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get taxes query for DataTables
     */
    public function getTaxesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->taxRepository->getTaxesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching taxes query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get taxes query for Select2 AJAX search
     */
    public function getAllTaxesQuery(array $filters = [])
    {
        try {
            return $this->taxRepository->getAllTaxesQuery($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching taxes query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search taxes for Select2 (AJAX with pagination)
     */
    public function searchForSelect2($search = '', $page = 1, $perPage = 30)
    {
        try {
            // Build filters for active taxes with search
            $filters = [
                'search' => $search,
                'active' => 1
            ];

            // Get query from repository
            $query = $this->taxRepository->getAllTaxesQuery($filters);

            // Count total for pagination
            $total = $query->count();

            // Get paginated taxes
            $taxes = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Format results for Select2
            $results = $taxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'text' => $tax->getTranslation('name', app()->getLocale()) . ' (' . $tax->tax_rate . '%)'
                ];
            });

            return [
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error searching taxes for Select2: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get tax by ID
     */
    public function getTaxById(int $id)
    {
        try {
            return $this->taxRepository->getTaxById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new tax with translations
     */
    public function createTax(array $data)
    {
        try {
            return $this->taxRepository->createTax($data);
        } catch (\Exception $e) {
            Log::error('Error creating tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update tax with translations
     */
    public function updateTax(int $id, array $data)
    {
        try {
            return $this->taxRepository->updateTax($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete tax
     */
    public function deleteTax(int $id)
    {
        try {
            return $this->taxRepository->deleteTax($id);
        } catch (\Exception $e) {
            Log::error('Error deleting tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active taxes
     */
    public function getActiveTaxes()
    {
        try {
            return $this->taxRepository->getActiveTaxes();
        } catch (\Exception $e) {
            Log::error('Error fetching active taxes: ' . $e->getMessage());
            throw $e;
        }
    }
}
