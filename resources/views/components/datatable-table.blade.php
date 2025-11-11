@props([
    'tableId' => 'dataTable',
    'columns' => [],
    'languages' => null,
])

<div class="table-responsive">
    <table id="{{ $tableId }}" class="table mb-0 table-bordered table-hover" style="width:100%">
        <thead>
            <tr class="userDatatable-header">
                {{-- ID Column --}}
                <th><span class="userDatatable-title">#</span></th>

                {{-- Language-based columns (if provided) --}}
                @if($languages)
                    @foreach ($languages as $language)
                        <th>
                            <span class="userDatatable-title"
                                @if ($language->rtl) dir="rtl" @endif>
                                {{ $columns['nameLabel'] ?? __('common.name') }} ({{ $language->name }})
                            </span>
                        </th>
                    @endforeach
                @endif

                {{-- Custom Columns --}}
                @if(isset($columns['additional']) && is_array($columns['additional']))
                    @foreach($columns['additional'] as $column)
                        <th>
                            <span class="userDatatable-title">{{ $column }}</span>
                        </th>
                    @endforeach
                @endif

                {{-- Actions Column --}}
                <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
