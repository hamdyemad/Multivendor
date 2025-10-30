{{-- Key Node --}}
<div class="tree-node tree-level-{{ $level }} tree-key-node">
    <div class="tree-node-content {{ ($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0) ? 'has-children' : '' }}">
        @if(($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0))
            <div class="tree-toggle">
                <i class="uil uil-angle-down"></i>
            </div>
        @else
            <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                <i class="uil uil-minus" style="color: #adb5bd;"></i>
            </div>
        @endif
        
        <div class="tree-info">
            <span class="tree-key-icon">
                <i class="uil uil-key-skeleton-alt"></i>
            </span>
            
            <div class="tree-names">
                <span class="tree-name-item" title="English">
                    {{ $variantKey->getTranslation('name', 'en') }}
                </span>
                <span class="tree-name-item" dir="rtl" title="Arabic">
                    {{ $variantKey->getTranslation('name', 'ar') }}
                </span>
            </div>

            @if($variantKey->variants && $variantKey->variants->count() > 0)
                <span class="children-count" title="{{ trans('catalogmanagement::variantsconfig.variants_count') }}">
                    {{ $variantKey->variants->count() }} {{ trans('catalogmanagement::variantsconfig.variants') }}
                </span>
            @endif
        </div>

        <div class="tree-actions">
            <a href="{{ route('admin.variant-keys.show', $variantKey->id) }}" 
               class="view" 
               title="{{ trans('common.view') }}">
                <i class="uil uil-eye"></i>
            </a>
        </div>
    </div>

    @if(($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0))
        <div class="tree-children expanded">
            {{-- Show Variant Configurations under this key --}}
            @if($variantKey->variants && $variantKey->variants->count() > 0)
                @foreach($variantKey->variants as $variant)
                    <div class="tree-node tree-level-{{ $level + 1 }} tree-variant-node">
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

                        {{-- Show children variants if any --}}
                        @if($variant->children && $variant->children->count() > 0)
                            <div class="tree-children expanded">
                                @foreach($variant->children as $childVariant)
                                    @include('catalogmanagement::variants-config.partials.variant-child-node', ['variant' => $childVariant, 'level' => $level + 2])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- Show Child Keys if any --}}
            @if($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)
                @foreach($variantKey->childrenKeys as $childKey)
                    @include('catalogmanagement::variants-config.partials.tree-node', ['variantKey' => $childKey, 'level' => $level + 1])
                @endforeach
            @endif
        </div>
    @endif
</div>
