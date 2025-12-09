<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait HumanDates
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
        ->timezone(config('app.timezone'))
        ->locale(app()->getLocale())
        ->translatedFormat('d M, Y, h:i A');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)
        ->timezone(config('app.timezone'))
        ->locale(app()->getLocale())
        ->translatedFormat('d M, Y, h:i A');
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return Carbon::parse($value)
        ->timezone(config('app.timezone'))
        ->locale(app()->getLocale())
        ->translatedFormat('d M, Y, h:i A');
    }

    public function getDiscountEndAtAttribute()
    {
        return Carbon::parse($this->attributes['discount_end_date'])
        ->timezone(config('app.timezone'))
        ->locale(app()->getLocale())
        ->translatedFormat('d M, Y, h:i A');
    }

    public function getValidUntilApiAttribute()
    {
        return Carbon::parse($this->attributes['valid_until'])
                ->timezone(config('app.timezone'))
                ->locale(app()->getLocale())
                ->translatedFormat('d M, Y, h:i A');
    }

    public function getValidFromApiAttribute()
    {
        return Carbon::parse($this->attributes['valid_from'])
                ->timezone(config('app.timezone'))
                ->locale(app()->getLocale())
                ->translatedFormat('d M, Y, h:i A');
    }
}
