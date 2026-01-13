<li>
    <span class="id-badge" style="font-size: 11px; padding: 2px 8px; background-color: #6c757d;">{{ $variant->id }}</span>
    <span class="variant-name ms-2">{{ $variant->getTranslation('name', 'en') }} / {{ $variant->getTranslation('name', 'ar') }}</span>
    @if($variant->color)
        <span class="color-preview ms-2" style="width: 20px; height: 20px; background-color: {{ $variant->color }}; display: inline-block; vertical-align: middle;"></span>
    @endif
    
    @if($variant->childrenRecursive && $variant->childrenRecursive->count() > 0)
        <ul>
            @foreach($variant->childrenRecursive as $child)
                @include('catalogmanagement::system-catalog.partials.variant-tree-item', ['variant' => $child])
            @endforeach
        </ul>
    @endif
</li>
