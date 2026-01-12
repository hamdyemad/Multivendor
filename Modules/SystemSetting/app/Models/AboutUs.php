<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUs extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'about_us';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the attachments for the about us
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Scope to filter by platform
     */
    public function scopePlatform(Builder $query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to filter about us
     */
    public function scopeFilter(Builder $query, $filters = [])
    {
        if (!empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }
        return $query;
    }

    /**
     * Get image fields for each section
     */
    public static function getImageFields(): array
    {
        $fields = [];
        for ($i = 1; $i <= 4; $i++) {
            $fields[] = "section_{$i}_image";
            $fields[] = "section_{$i}_sub_section_1_icon";
            $fields[] = "section_{$i}_sub_section_2_icon";
        }
        return $fields;
    }

    /**
     * Get translation fields for each section
     * Each section has: title, text, 2 sub_sections (subtitle, text), 4 bullets
     */
    public static function getTranslatableFields(): array
    {
        $fields = [];
        
        for ($i = 1; $i <= 4; $i++) {
            // Main section fields
            $fields[] = "section_{$i}_title";
            $fields[] = "section_{$i}_text";
            
            // Sub section 1
            $fields[] = "section_{$i}_sub_section_1_subtitle";
            $fields[] = "section_{$i}_sub_section_1_text";
            
            // Sub section 2
            $fields[] = "section_{$i}_sub_section_2_subtitle";
            $fields[] = "section_{$i}_sub_section_2_text";
            
            // Bullets
            $fields[] = "section_{$i}_bullet_1";
            $fields[] = "section_{$i}_bullet_2";
            $fields[] = "section_{$i}_bullet_3";
            $fields[] = "section_{$i}_bullet_4";
        }
        
        return $fields;
    }
    
    /**
     * Get non-translatable link fields (URLs don't need translation)
     */
    public static function getLinkFields(): array
    {
        return [
            'section_1_link',
            'section_2_video_link',
        ];
    }
}
