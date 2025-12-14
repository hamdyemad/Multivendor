<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Http\Requests\ServiceTermsRequest;
use Modules\SystemSetting\app\Services\ServiceTermsService;

class ServiceTermsController extends Controller
{
    protected $serviceTermsService;

    public function __construct(ServiceTermsService $serviceTermsService)
    {
        $this->serviceTermsService = $serviceTermsService;
    }

    public function index()
    {
        $languages = Language::all();
        $serviceTerms = $this->serviceTermsService->getServiceTerms();
        if ($serviceTerms) {
            $serviceTerms->load('translations');
        }
        return view('systemsetting::service-terms.form', compact('serviceTerms', 'languages'));
    }

    public function update(ServiceTermsRequest $request)
    {
        try {
            $data = $request->validated();

            if (empty($data['description'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::service-terms.error_updating') . ': No description data received'
                ], 400);
            }

            $serviceTerms = $this->serviceTermsService->updateServiceTerms($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::service-terms.updated_successfully'),
                    'data' => $serviceTerms
                ]);
            }

            return redirect()
                ->route('admin.system-settings.service-terms.index')
                ->with('success', __('systemsetting::service-terms.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::service-terms.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::service-terms.error_updating') . ': ' . $e->getMessage());
        }
    }
}
