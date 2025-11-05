<?php

namespace Modules\CategoryManagment\app\Services;

use Modules\CategoryManagment\app\Interfaces\ActivityRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = [], int $perPage)
    {
        try {
            return $this->activityRepository->getAllActivities($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching activities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activities query for DataTables
     */
    public function getActivitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->activityRepository->getActivitiesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching activities query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activities query for Select2 AJAX search
     */
    public function getAllActivitiesQuery(array $filters = [])
    {
        try {
            return $this->activityRepository->getAllActivitiesQuery($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching activities query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search activities for Select2 (AJAX with pagination)
     */
    public function searchForSelect2($search = '', $page = 1, $perPage = 30)
    {
        try {
            // Build filters for active activities with search
            $filters = [
                'search' => $search,
                'active' => 1
            ];
            
            // Get query from repository
            $query = $this->activityRepository->getAllActivitiesQuery($filters);
            
            // Count total for pagination
            $total = $query->count();
            
            // Get paginated activities
            $activities = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Format results for Select2
            $results = $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'text' => $activity->getTranslation('name', app()->getLocale())
                ];
            });

            return [
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error searching activities for Select2: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity by ID
     */
    public function getActivityById($id)
    {
        try {
            return $this->activityRepository->getActivityById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new activity with translations
     */
    public function createActivity(array $data)
    {
        try {
            // Validate and prepare data
            $preparedData = $this->prepareActivityData($data);
            
            return $this->activityRepository->createActivity($preparedData);
        } catch (\Exception $e) {
            Log::error('Error creating activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update activity with translations
     */
    public function updateActivity(int $id, array $data)
    {
        try {
            // Validate and prepare data
            $preparedData = $this->prepareActivityData($data);
            
            return $this->activityRepository->updateActivity($id, $preparedData);
        } catch (\Exception $e) {
            Log::error('Error updating activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete activity
     */
    public function deleteActivity(int $id)
    {
        try {
            return $this->activityRepository->deleteActivity($id);
        } catch (\Exception $e) {
            Log::error('Error deleting activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active activities
     */
    public function getActiveActivities()
    {
        try {
            return $this->activityRepository->getActiveActivities();
        } catch (\Exception $e) {
            Log::error('Error fetching active activities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prepare activity data for storage
     */
    private function prepareActivityData(array $data): array
    {
        return [
            'translations' => $data['translations'] ?? [],
            'active' => $data['active'] ?? 0,
        ];
    }
}
