<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SystemSetting\app\Models\PrivacyPolicy;
use Modules\SystemSetting\app\Models\ReturnPolicy;
use Modules\SystemSetting\app\Models\ServiceTerms;
use Modules\SystemSetting\app\Models\TermsConditions;

class SiteInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $return_policy = ReturnPolicy::first();
        $service_terms = ServiceTerms::first();
        $privacy_and_policy = PrivacyPolicy::first();
        $terms_and_conditions = TermsConditions::first();

        return [
            'id' => $this->id,
            'address' => $this->address,
            'facebook_url' => $this->facebook_url,
            'linkedin_url' => $this->linkedin_url,
            'twitter_url' => $this->twitter_url,
            'instagram_url' => $this->instagram_url,
            'phone_1' => $this->phone_1,
            'phone_2' => $this->phone_2,
            'email' => $this->email,
            'google_maps_url' => $this->google_maps_url,
            'return_policy' => $return_policy?->description ?? '',
            'service_terms' => $service_terms?->description ?? '',
            'privacy_and_policy' => $privacy_and_policy?->description ?? '',
            'terms_and_conditions' => [
                'title' => $terms_and_conditions?->title ?? '',
                'description' => $terms_and_conditions?->description ?? '',
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
