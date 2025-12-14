<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Http\Requests\TermsConditionsRequest;
use Modules\SystemSetting\app\Services\TermsConditionsService;

class TermsConditionsController extends Controller
{
    protected $termsConditionsService;

    public function __construct(TermsConditionsService $termsConditionsService)
    {
        $this->termsConditionsService = $termsConditionsService;
    }

    public function index()
    {
        $languages = Language::all();
        $termsConditions = $this->termsConditionsService->getTermsConditions();
        // Load translations relationship to ensure they're available in the view
        if ($termsConditions) {
            $termsConditions->load('translations');
        }
        return view('systemsetting::terms-conditions.form', compact('termsConditions', 'languages'));
    }

    public function update(TermsConditionsRequest $request)
    {
        try {
            $data = $request->validated();

            // Verify data is being received
            if (empty($data['title']) && empty($data['description'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::terms-conditions.error_updating') . ': No data received'
                ], 400);
            }

            $termsConditions = $this->termsConditionsService->updateTermsConditions($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::terms-conditions.updated_successfully'),
                    'data' => $termsConditions
                ]);
            }

            return redirect()
                ->route('admin.system-settings.terms-conditions.index')
                ->with('success', __('systemsetting::terms-conditions.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::terms-conditions.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::terms-conditions.error_updating') . ': ' . $e->getMessage());
        }
    }
}
