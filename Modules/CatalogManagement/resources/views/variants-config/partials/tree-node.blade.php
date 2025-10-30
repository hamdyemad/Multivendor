<div class="tree-node tree-level-{{ $level }}">
    <div class="tree-node-content {{ $variantsConfig->children && $variantsConfig->children->count() > 0 ? 'has-children' : '' }}">
        @if($variantsConfig->children && $variantsConfig->children->count() > 0)
            <div class="tree-toggle">
                <i class="uil uil-angle-down"></i>
            </div>
        @else
            <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                <i class="uil uil-minus" style="color: #adb5bd;"></i>
            </div>
        @endif
        
        <div class="tree-info">
            <span class="tree-id">#{{ $variantsConfig->id }}</span>
            
            <div class="tree-names">
                <span class="tree-name-item" title="English">
                    {{ $variantsConfig->getTranslation('name', 'en') }}
                </span>
                <span class="tree-name-item" dir="rtl" title="Arabic">
                    {{ $variantsConfig->getTranslation('name', 'ar') }}
                </span>
            </div>

            @if($variantsConfig->parent_data)
                <span class="tree-parent-name me-1" title="{{ trans('catalogmanagement::variantsconfig.parent') }}">
                    <i class="uil uil-sitemap"></i> {{ $variantsConfig->parent_data->getTranslation('name', 'en') }}
                </span>
            @endif

            
            @if($variantsConfig->key)
                @php
                    $keyTranslation = $variantsConfig->key->getTranslation('name', app()->getLocale());
                @endphp
                <span class="tree-key-name me-1" title="{{ trans('catalogmanagement::variantsconfig.key') }}">
                    <i class="uil uil-key-skeleton-alt"></i> {{ $keyTranslation }}
                </span>
            @endif
        </div>

        <div class="tree-actions">
            <a href="{{ route('admin.variants-configurations.show', $variantsConfig->id) }}" 
               class="view" 
               title="{{ trans('common.view') }}">
                <i class="uil uil-eye"></i>
            </a>
        </div>
    </div>

    @if($variantsConfig->children && $variantsConfig->children->count() > 0)
        <div class="tree-children expanded">
            @foreach($variantsConfig->children as $child)
                @include('catalogmanagement::variants-config.partials.tree-node', ['variantsConfig' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
