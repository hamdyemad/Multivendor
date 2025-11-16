<?php

namespace App\Traits;

use App\Models\Language;
use App\Models\Translation as TranslationModel;

trait Translation
{
  public function translations()
  {
      return $this->morphMany(TranslationModel::class, 'translatable')->withTrashed();
  }


  public function getTranslation(string $key, string $locale = 'en')
  {
    $lang = Language::where('code', $locale)->first();
    if($lang) {
      $translation = $this->translations()
          ->where('lang_id', $lang->id)
          ->where('lang_key', $key)
          ->first();
      return $translation ? $translation->lang_value : null;
    }
    return null;
  }

  public function setTranslation(string $key, string $locale, string $value)
  {
      $lang = Language::where('code', $locale)->first();
      if($lang) {
          $translation = $this->translations()
              ->firstOrNew(['lang_id' => $lang->id, 'lang_key' => $key]);
          $translation->lang_value = $value;
          $translation->save();
      }
  }
}
