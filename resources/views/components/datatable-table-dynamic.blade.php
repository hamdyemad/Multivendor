@props([
    'tableId' => 'dataTable',
    'columns' => [],
    'showIdColumn' => true,
    'showActionsColumn' => true,
])

<div class="table-responsive">
    <table id="{{ $tableId }}" class="table mb-0 table-bordered table-hover" style="width:100%">
        <thead>
            <tr class="userDatatable-header">
                {{-- ID Column --}}
                @if($showIdColumn)
                    <th><span class="userDatatable-title">#</span></th>
                @endif

                {{-- Dynamic Columns --}}
                @foreach($columns as $column)
                    <th>
                        <span class="userDatatable-title"
                            @if(isset($column['rtl']) && $column['rtl']) dir="rtl" @endif>
                            {{ $column['label'] }}
                        </span>
                    </th>
                @endforeach

                {{-- Actions Column --}}
                @if($showActionsColumn)
                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                @endif
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
