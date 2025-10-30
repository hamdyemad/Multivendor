<div class="tree-node tree-level-{{ $level }}">
    <div class="tree-node-content {{ $variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0 ? 'has-children' : '' }}">
        @if($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)
            <div class="tree-toggle">
                <i class="uil uil-angle-down"></i>
            </div>
        @else
            <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                <i class="uil uil-minus" style="color: #adb5bd;"></i>
            </div>
        @endif
        
        <div class="tree-info">
            <div class="tree-names">
                @foreach($languages as $language)
                    @php
                        $translation = $variantKey->translations->where('lang_id', $language->id)
                            ->where('lang_key', 'name')
                            ->first();
                    @endphp
                    @if($translation && $translation->lang_value)
                        <span class="tree-name-item" @if($language->rtl) dir="rtl" @endif 
                              title="{{ $language->name }}">
                            {{ $translation->lang_value }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="tree-actions">
            <a href="{{ route('admin.variant-keys.show', $variantKey->id) }}" 
               class="view" 
               title="{{ trans('common.view') }}">
                <i class="uil uil-eye"></i>
            </a>
        </div>
    </div>

    @if($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)
        <div class="tree-children expanded">
            @foreach($variantKey->childrenKeys as $child)
                @include('catalogmanagement::variant-key.partials.tree-node', ['variantKey' => $child, 'languages' => $languages, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
