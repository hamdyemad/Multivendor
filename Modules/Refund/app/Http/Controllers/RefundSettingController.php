<?php

namespace Modules\Refund\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Refund\app\Models\RefundSetting;
use Modules\Refund\app\Http\Requests\UpdateRefundSettingRequest;

class RefundSettingController extends Controller
{
    /**
     * Display refund settings
     */
    public function index()
    {
        $settings = RefundSetting::getInstance();
        
        return view('refund::settings.index', compact('settings'));
    }
    
    /**
     * Update refund settings
     */
    public function update(UpdateRefundSettingRequest $request)
    {
        $settings = RefundSetting::getInstance();
        
        $settings->update($request->validated());
        
        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => trans('refund::refund.messages.settings_updated'),
            ]);
        }
        
        return back()->with('success', trans('refund::refund.messages.settings_updated'));
    }
}
