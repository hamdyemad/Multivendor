<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Services\ActivityService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\CategoryManagment\app\Interfaces\ActivityRepositoryInterface;

class ActivityAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected ActivityService $activityService,
        protected ActivityRepositoryInterface $activityRepositoryInterface
        ) {}

    /**
     * Datatable endpoint for server-side processing
     */
    public function getDataTable($data)
    {
        try {
            // Get pagination parameters
            $perPage = $data['per_page'] ?? $data['length'] ?? 10;
            $page = $data['page'] ?? 1;

            // Get sorting parameters
            $orderColumnIndex = $data['orderColumnIndex'] ?? 0;
            $orderDirection = $data['orderDirection'] ?? 'desc';

            // Get filter parameters
            $filters = [
                'search' => $data['search'],
                'active' => $data['active'],
                'created_date_from' => $data['created_date_from'],
                'created_date_to' => $data['created_date_to'],
            ];

            // Get languages
            $languages = $this->languageService->getAll();

            // Get total and filtered counts
            $totalRecords = $this->activityRepositoryInterface->getActivitiesQuery([])->count();
            $filteredRecords = $this->activityRepositoryInterface->getActivitiesQuery($filters)->count();

            // Determine sort column
            // Column 0 is 'index' (row number) - not sortable
            // Columns 1 to count($languages) are name translations
            // Then: active, created_at
            $orderBy = null;
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
                // Sorting by translated name column
                $languageIndex = $orderColumnIndex - 1;
                $selectedLanguage = $languages->values()->get($languageIndex);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($orderColumnIndex == count($languages) + 1) {
                $orderBy = 'active';
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'created_at';
            } else {
                // Default sorting
                $orderBy = 'id';
            }

            // Get activities with pagination and sorting
            $activitiesQuery = $this->activityRepositoryInterface->getActivitiesQuery($filters, $orderBy, $orderDirection);
            $activities = $activitiesQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($activities as $index => $activity) {
                $rowData = [
                    'index' => $index + 1,
                    'id' => $activity->id,
                    'translations' => [],
                    'active' => $activity->active,
                    'created_at' => $activity->created_at,
                ];

                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $activity->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }

                // Add first translation name for delete modal
                $firstTranslation = $activity->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';

                $data[] = $rowData;
            }

            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $activities
            ];

        } catch (\Exception $e) {
            Log::error('Error in ActivityAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }

}
