<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Services\VariantsConfigurationService;
use Yajra\DataTables\Facades\DataTables;

class VariantsConfigurationAction
{
    public function __construct(
        protected VariantsConfigurationService $variantsConfigService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function getDataTable($data)
    {
        try {
            // Get pagination parameters
            $perPage = $data['per_page'] ?? $data['length'] ?? 10;
            $page = $data['page'] ?? 1;

            // Build query with filters
            $query = VariantsConfiguration::query()
                ->with(['translations', 'key.translations', 'parent_data', 'children']);

            // Apply search filter
            if (!empty($data['search'])) {
                $search = $data['search'];
                $query->where(function($q) use ($search) {
                    $q->where('value', 'like', '%' . $search . '%')
                      ->orWhereHas('translations', function($query) use ($search) {
                          $query->where('lang_key', 'name')
                                ->where('lang_value', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('key.translations', function($query) use ($search) {
                          $query->where('lang_key', 'name')
                                ->where('lang_value', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('parent_data', function($query) use ($search) {
                          $query->where('value', 'like', '%' . $search . '%');
                      });
                });
            }

            // Apply date filters
            if (!empty($data['created_date_from'])) {
                $query->whereDate('created_at', '>=', $data['created_date_from']);
            }

            if (!empty($data['created_date_to'])) {
                $query->whereDate('created_at', '<=', $data['created_date_to']);
            }

            // Get total and filtered counts
            $totalRecords = \Modules\CatalogManagement\app\Models\VariantsConfiguration::count();
            $filteredRecords = $query->count();

            // Get all variants configurations
            $variantsConfigsCollection = $query->get();

            // Apply sorting using Eloquent collections
            if (isset($data['orderColumnIndex']) && isset($data['orderDirection'])) {
                $columnIndex = $data['orderColumnIndex'];
                $direction = $data['orderDirection'];

                switch ($columnIndex) {
                    case 0: // ID column
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy('id')
                            : $variantsConfigsCollection->sortByDesc('id');
                        break;

                    case 1: // Name (EN) column
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy(function($item) {
                                $trans = $item->translations->where('lang_key', 'name')->where('lang_id', 1)->first();
                                return $trans ? strtolower($trans->lang_value) : '';
                              })
                            : $variantsConfigsCollection->sortByDesc(function($item) {
                                $trans = $item->translations->where('lang_key', 'name')->where('lang_id', 1)->first();
                                return $trans ? strtolower($trans->lang_value) : '';
                              });
                        break;

                    case 2: // Name (AR) column
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy(function($item) {
                                $trans = $item->translations->where('lang_key', 'name')->where('lang_id', 2)->first();
                                return $trans ? $trans->lang_value : '';
                              })
                            : $variantsConfigsCollection->sortByDesc(function($item) {
                                $trans = $item->translations->where('lang_key', 'name')->where('lang_id', 2)->first();
                                return $trans ? $trans->lang_value : '';
                              });
                        break;

                    case 3: // Type column
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy('type')
                            : $variantsConfigsCollection->sortByDesc('type');
                        break;

                    case 4: // Value column
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy('value')
                            : $variantsConfigsCollection->sortByDesc('value');
                        break;

                    case 6: // Created At column (updated index after removing parent and children_count columns)
                        $variantsConfigsCollection = $direction === 'asc'
                            ? $variantsConfigsCollection->sortBy('created_at')
                            : $variantsConfigsCollection->sortByDesc('created_at');
                        break;

                    default:
                        $variantsConfigsCollection = $variantsConfigsCollection->sortByDesc('created_at');
                        break;
                }
            } else {
                // Default sorting by created_at
                $variantsConfigsCollection = $variantsConfigsCollection->sortByDesc('created_at');
            }

            // Manual pagination
            $offset = ($page - 1) * $perPage;
            $variantsConfigs = $variantsConfigsCollection->slice($offset, $perPage)->values();

            // Create paginator instance
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $variantsConfigs,
                $variantsConfigsCollection->count(),
                $perPage,
                $page,
                ['path' => request()->url()]
            );

            // Build data array for DataTables
            $responseData = [];
            foreach ($variantsConfigs as $index => $variantsConfig) {
                // Get name translations
                $nameEn = '-';
                $nameAr = '-';

                if ($variantsConfig->translations) {
                    $enTranslation = $variantsConfig->translations
                        ->where('lang_key', 'name')
                        ->where('lang_id', 1) // English language ID
                        ->first();
                    $arTranslation = $variantsConfig->translations
                        ->where('lang_key', 'name')
                        ->where('lang_id', 2) // Arabic language ID
                        ->first();

                    $nameEn = $enTranslation ? $enTranslation->lang_value : '-';
                    $nameAr = $arTranslation ? $arTranslation->lang_value : '-';
                }

                $rowData = [
                    'index' => $index + 1,
                    'id' => $variantsConfig->id,
                    'name_en' => $nameEn,
                    'name_ar' => $nameAr,
                    'key_name' => '-',
                    'parent' => '-',
                    'created_at' => $variantsConfig->created_at,
                ];

                // Add key name
                if ($variantsConfig->key) {
                    $rowData['key_name'] = $variantsConfig->key->getTranslation('name', app()->getLocale());
                }

                // Add parent information if exists
                if ($variantsConfig->parent_data) {
                    $rowData['parent'] = $variantsConfig->parent_data->getTranslation('name', app()->getLocale());
                }

                $responseData[] = $rowData;
            }

            return [
                'data' => $responseData,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $paginator
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in VariantsConfigurationAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }

}
