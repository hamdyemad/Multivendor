<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface OccasionRepositoryInterface
{
    /**
     * Get all occasions with optional filters
     */
    public function getAllOccasions(array $filters = [], $perPage);

    /**
     * Get occasion by ID
     */
    public function getOccasionById($id);

    /**
     * Create new occasion
     */
    public function createOccasion(array $data);

    /**
     * Update occasion
     */
    public function updateOccasion($id, array $data);

    /**
     * Delete occasion
     */
    public function deleteOccasion($id);

    /**
     * Get active occasions
     */
    public function getActiveOccasions();

    /**
     * Toggle occasion status
     */
    public function toggleOccasionStatus($id);

    /**
     * Count total occasions
     */
    public function count(): int;
}
