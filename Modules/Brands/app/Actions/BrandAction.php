<?php

namespace Modules\Brands\app\Actions;

use Modules\Brands\app\Services\BrandService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Brands\app\Interfaces\BrandRepositoryInterface;

class BrandAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected BrandRepositoryInterface $brandRepositoryInterface
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
            
            // Get filter parameters
            $filters = [
                'search' => $data['search'],
                'active' => $data['active'],
                'created_date_from' => $data['created_date_from'],
                'created_date_to' => $data['created_date_to'],
            ];
            
            // Get total and filtered counts
            $totalRecords = $this->brandRepositoryInterface->getBrandsQuery([])->count();
            $filteredRecords = $this->brandRepositoryInterface->getBrandsQuery($filters)->count();
            
            // Get brands with pagination
            $brandsQuery = $this->brandRepositoryInterface->getBrandsQuery($filters);
            $brands = $brandsQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Format data for DataTables
            $data = [];
            foreach ($brands as $brand) {
                $row = [];
                
                // ID column
                $row[] = $brand->id;
                
                // Logo column
                $logoHtml = '-';
                if ($brand->logo) {
                    $logoUrl = asset('storage/' . $brand->logo->path);
                    $logoHtml = '<img src="' . $logoUrl . '" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;" />';
                }
                $row[] = $logoHtml;
                
                // Name columns for each language
                foreach ($languages as $language) {
                    $translation = $brand->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $name = $translation ? $translation->lang_value : '-';
                    
                    if ($language->rtl) {
                        $row[] = '<span dir="rtl">' . e($name) . '</span>';
                    } else {
                        $row[] = e($name);
                    }
                }
                
                // Active status column
                $activeStatus = $brand->active 
                    ? '<span class="badge badge-success badge-lg badge-round">' . __('common.active') . '</span>'
                    : '<span class="badge badge-danger badge-lg badge-round">' . __('common.inactive') . '</span>';
                $row[] = $activeStatus;
                
                // Created at column
                $row[] = $brand->created_at->format('Y-m-d H:i');
                
                // Actions
                $actionsHtml = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                        <li>
                            <a href="' . route('admin.brands.show', $brand->id) . '" 
                            class="view" 
                            title="' . e(trans('common.view')) . '">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.brands.edit', $brand->id) . '" 
                            class="edit" 
                            title="' . e(trans('common.edit')) . '">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                            class="remove delete-brand" 
                            title="' . e(trans('common.delete')) . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-delete-brand"
                            data-item-id="' . $brand->id . '"
                            data-item-name="' . e($brand->translations->where("lang_key", "name")->first()->lang_value ?? "") . '"
                            data-url="' . route('admin.brands.destroy', $brand->id) . '">
                                <i class="uil uil-trash-alt"></i>
                            </a>
                        </li>
                    </ul>';

                $row[] = $actionsHtml;
                
                $data[] = $row;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $brands
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in BrandAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
