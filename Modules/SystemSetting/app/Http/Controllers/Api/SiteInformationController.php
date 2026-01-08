<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Actions\AdAction;
use Modules\SystemSetting\app\Http\Requests\AdRequest;
use Modules\SystemSetting\app\Http\Resources\Api\AdsResource;
use Modules\SystemSetting\app\Http\Resources\Api\FeatureResource;
use Modules\SystemSetting\app\Http\Resources\Api\FooterContentResource;
use Modules\SystemSetting\app\Http\Resources\Api\SiteInformationResource;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Models\Feature;
use Modules\SystemSetting\app\Models\FooterContent;
use Modules\SystemSetting\app\Models\SiteInformation;
use Modules\SystemSetting\app\Services\Api\AdApiService;
use Yajra\DataTables\Facades\DataTables;

class SiteInformationController extends Controller
{
    use Res;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siteInformation = SiteInformation::first();
        
        if (!$siteInformation) {
            return $this->sendRes(__('main.success'), true, [
                'id' => null,
                'address' => '',
                'facebook_url' => '',
                'linkedin_url' => '',
                'twitter_url' => '',
                'instagram_url' => '',
                'phone_1' => '',
                'phone_2' => '',
                'email' => '',
                'google_maps_url' => '',
                'return_policy' => '',
                'service_terms' => '',
                'privacy_and_policy' => '',
                'terms_and_conditions' => [
                    'title' => '',
                    'description' => '',
                ],
                'created_at' => null,
                'updated_at' => null,
            ]);
        }
        
        return $this->sendRes(__('main.success'), true, new SiteInformationResource($siteInformation));
    }

}
