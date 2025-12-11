@props(['label', 'model', 'fieldName', 'languages', 'type' => 'text'])

<div class="col-md-12">
    <div class="view-item box-items-translations">
        <label class="il-gray fs-14 fw-500 mb-10">{{ $label }}</label>
        <div class="row">
            @php
                $current = app()->getLocale();
            @endphp

            @foreach(
                $languages->sortBy(function($lang) use ($current) {
                    return $current == 'ar'
                        ? ($lang->code == 'ar' ? 0 : 1)
                        : ($lang->code == 'en' ? 0 : 1);
                })
            as $lang)
                @php
                    $translation = $model->getTranslation($fieldName, $lang->code);
                @endphp

                <div class="col-md-6 mb-3">
                    <div style="
                        padding: 12px;
                        background: #f8f9fa;
                        border-radius: 6px;
                        {{ $lang->code == 'ar' ? 'border-right: 3px solid #5f63f2;' : 'border-left: 3px solid #5f63f2;' }}
                    ">

                        <small class="text-muted d-block mb-2"
                            style="{{ $lang->code == 'ar' ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;' }}">
                            <span class="badge
                                {{ $lang->code == 'en' ? 'bg-primary' : 'bg-success' }}
                                text-white px-2 py-1 round-pill fw-bold"
                                style="font-size: 10px;">
                                {{ strtoupper($lang->code) }}
                            </span>
                        </small>

                        <div class="fs-15 color-dark mb-0 fw-500"
                            style="
                                {{ $lang->code == 'ar'
                                    ? 'direction: rtl; text-align: right; font-family: Cairo, Segoe UI, Tahoma, Geneva, Verdana, sans-serif;'
                                    : 'direction: ltr; text-align: left;'
                                }}
                            ">
                            @if($type === 'keywords')
                                @php
                                    $keywords = [];
                                    if ($translation) {
                                        $decoded = json_decode($translation, true);
                                        if (is_array($decoded)) {
                                            $keywords = $decoded;
                                        } else {
                                            $keywords = array_map('trim', explode(',', $translation));
                                            $keywords = array_filter($keywords);
                                        }
                                    }
                                @endphp
                                @if(count($keywords) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($keywords as $keyword)
                                            <span class="badge badge-lg badge-round bg-info text-white" style="font-size: 12px; padding: 6px 10px; @if($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                {{ trim($keyword) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @else
                                @if($translation)
                                    {{ $translation }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
