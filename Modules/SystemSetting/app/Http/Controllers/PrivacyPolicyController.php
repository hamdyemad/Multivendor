<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Http\Requests\PrivacyPolicyRequest;
use Modules\SystemSetting\app\Services\PrivacyPolicyService;

class PrivacyPolicyController extends Controller
{
    protected $privacyPolicyService;

    public function __construct(PrivacyPolicyService $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
    }

    public function index()
    {
        $languages = Language::all();
        $privacyPolicy = $this->privacyPolicyService->getPrivacyPolicy();
        // Load translations relationship to ensure they're available in the view
        if ($privacyPolicy) {
            $privacyPolicy->load('translations');
        }
        return view('systemsetting::privacy-policy.form', compact('privacyPolicy', 'languages'));
    }

    public function update(PrivacyPolicyRequest $request)
    {
        try {
            $data = $request->validated();

            // Verify data is being received
            if (empty($data['description'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::privacy-policy.error_updating') . ': No description data received'
                ], 400);
            }

            $privacyPolicy = $this->privacyPolicyService->updatePrivacyPolicy($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::privacy-policy.updated_successfully'),
                    'data' => $privacyPolicy
                ]);
            }

            return redirect()
                ->route('admin.system-settings.privacy-policy.index')
                ->with('success', __('systemsetting::privacy-policy.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::privacy-policy.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::privacy-policy.error_updating') . ': ' . $e->getMessage());
        }
    }
}
