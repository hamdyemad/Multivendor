<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\OccasionRepositoryInterface;

class OccasionService
{
    protected $occasionRepository;

    public function __construct(
        OccasionRepositoryInterface $occasionRepository
    ) {
        $this->occasionRepository = $occasionRepository;
    }

    /**
     * Get occasions query with filters
     */
    public function getAllOccasions(array $filters = [], $perPage)
    {
        return $this->occasionRepository->getAllOccasions($filters, $perPage);
    }


    /**
     * Get occasion by ID
     */
    public function getOccasionById($id)
    {
        return $this->occasionRepository->getOccasionById($id);
    }

    /**
     * Create new occasion
     */
    public function createOccasion(array $data)
    {
        return $this->occasionRepository->createOccasion($data);
    }

    /**
     * Update occasion
     */
    public function updateOccasion($id, array $data)
    {
        return $this->occasionRepository->updateOccasion($id, $data);
    }

    /**
     * Delete occasion
     */
    public function deleteOccasion($id)
    {
        return $this->occasionRepository->deleteOccasion($id);
    }

    /**
     * Toggle occasion status
     */
    public function toggleOccasionStatus($id)
    {
        return $this->occasionRepository->toggleOccasionStatus($id);
    }
}
