@props([
    'name',
    'label' => null,
    'labelAr' => null,
    'placeholder' => null,
    'placeholderAr' => null,
    'type' => 'text',
    'required' => false,
    'value' => null,
    'rows' => null,
    'languages' => [],
    'model' => null,
    'oldPrefix' => 'translations',
    'tags' => false,
    'cols' => 6,
    'inputClass' => null,
])

<div class="row">
    @foreach ($languages as $language)
        <div
            class="col-md-{{ $cols }} mb-25 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
            <div class="form-group" data-lang="{{ $language->code }}">
                <label for="translation_{{ $language->id }}_{{ $name }}"
                    class="il-gray fs-14 fw-500 mb-10 d-block"
                    @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                    @else
                        dir="ltr" @endif>
                    @if ($language->code == 'ar')
                        {{ $labelAr ?? $label }} ({{ $language->name }}) @if ($required)
                            <span class="text-danger">*</span>
                        @endif
                    @else
                        {{ $label }} ({{ $language->name }}) @if ($required)
                            <span class="text-danger">*</span>
                        @endif
                    @endif
                </label>

                @if ($tags)
                    <div @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl" @else dir="ltr" @endif>
                        <x-tags-input name="{{ $oldPrefix }}[{{ $language->id }}][{{ $name }}]"
                            :value="isset($model)
                                ? $model->getTranslation($name, $language->code) ?? ''
                                : old($oldPrefix . '.' . $language->id . '.' . $name, '')"
                            placeholder="{{ $language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder }}"
                            :rtl="$language->code == 'ar'" dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}" />
                    </div>
                @elseif($type === 'textarea')
                    <textarea
                        class="{{ $inputClass }} form-control ip-gray radius-xs b-light px-15 @error($oldPrefix . '.' . $language->id . '.' . $name) is-invalid @enderror"
                        id="translation_{{ $language->id }}_{{ $name }}"
                        name="{{ $oldPrefix }}[{{ $language->id }}][{{ $name }}]"
                        @if ($rows) rows="{{ $rows }}" @endif
                        placeholder="{{ $language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder }}"
                        @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                        @else
                            dir="ltr" @endif
                        data-lang="{{ $language->code }}">{{ isset($model) ? $model->getTranslation($name, $language->code) ?? '' : old($oldPrefix . '.' . $language->id . '.' . $name) }}</textarea>
                @else
                    <input type="{{ $type }}"
                        class="form-control ih-medium ip-gray radius-xs b-light px-15 @error($oldPrefix . '.' . $language->id . '.' . $name) is-invalid @enderror"
                        id="translation_{{ $language->id }}_{{ $name }}"
                        name="{{ $oldPrefix }}[{{ $language->id }}][{{ $name }}]"
                        value="{{ isset($model) ? $model->getTranslation($name, $language->code) ?? '' : old($oldPrefix . '.' . $language->id . '.' . $name) }}"
                        placeholder="{{ $language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder }}"
                        @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                        @else
                            dir="ltr" @endif
                        data-lang="{{ $language->code }}">
                @endif

                @error($oldPrefix . '.' . $language->id . '.' . $name)
                    <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endforeach
</div>
