{{-- Nested Variant Node --}}
<div class="tree-node tree-level-{{ $level }} tree-variant-node">
    <div class="tree-node-content {{ $variant->children && $variant->children->count() > 0 ? 'has-children' : '' }}">
        @if($variant->children && $variant->children->count() > 0)
            <div class="tree-toggle">
                <i class="uil uil-angle-down"></i>
            </div>
        @else
            <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                <i class="uil uil-circle" style="color: #adb5bd; font-size: 8px;"></i>
            </div>
        @endif
        
        <div class="tree-info">
            <span class="tree-variant-icon">
                <i class="uil uil-cube"></i>
            </span>
            {{-- <span class="tree-id">#{{ $variant->id }}</span> --}}
            
            <div class="tree-names">
                <span class="tree-name-item" title="English">
                    {{ $variant->getTranslation('name', 'en') }}
                </span>
                <span class="tree-name-item" dir="rtl" title="Arabic">
                    {{ $variant->getTranslation('name', 'ar') }}
                </span>
            </div>

            @if($variant->type)
                <span class="tree-type-badge" title="{{ trans('catalogmanagement::variantsconfig.type') }}">
                    <i class="uil uil-{{ $variant->type == 'color' ? 'palette' : 'text' }}"></i> 
                    {{ trans('catalogmanagement::variantsconfig.' . $variant->type) }}
                </span>
            @endif

            @if($variant->value)
                @if($variant->type == 'color')
                    <span class="tree-color-value" title="{{ $variant->value }}">
                        <span class="color-preview" style="background-color: {{ $variant->value }};"></span>
                        {{ $variant->value }}
                    </span>
                @else
                    <span class="tree-text-value" title="{{ trans('catalogmanagement::variantsconfig.value') }}">
                        {{ $variant->value }}
                    </span>
                @endif
            @endif
        </div>

        <div class="tree-actions">
            <a href="{{ route('admin.variants-configurations.show', $variant->id) }}" 
               class="view" 
               title="{{ trans('common.view') }}">
                <i class="uil uil-eye"></i>
            </a>
        </div>
    </div>

    {{-- Show children variants recursively --}}
    @if($variant->children && $variant->children->count() > 0)
        <div class="tree-children expanded">
            @foreach($variant->children as $childVariant)
                @include('catalogmanagement::variants-config.partials.variant-child-node', ['variant' => $childVariant, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
