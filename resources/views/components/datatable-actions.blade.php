@props([
    'actions' => [],
])

<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
    @foreach($actions as $action)
        @if($action['type'] === 'link')
            <a href="{{ $action['url'] }}" 
               class="{{ $action['class'] ?? 'btn btn-primary table_action_father' }}" 
               title="{{ $action['title'] ?? '' }}"
               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif>
                <i class="{{ $action['icon'] }} table_action_icon"></i>
            </a>
        @elseif($action['type'] === 'button')
            <a href="javascript:void(0);" 
               class="{{ $action['class'] ?? 'btn btn-primary table_action_father' }}" 
               title="{{ $action['title'] ?? '' }}"
               @if(isset($action['modal'])) data-bs-toggle="modal" data-bs-target="{{ $action['modal'] }}" @endif
               @foreach($action['data'] ?? [] as $key => $value)
                   data-{{ $key }}="{{ $value }}"
               @endforeach>
                <i class="{{ $action['icon'] }} table_action_icon"></i>
            </a>
        @elseif($action['type'] === 'form')
            <form action="{{ $action['url'] }}" method="POST" class="d-inline">
                @csrf
                @if(isset($action['method']))
                    @method($action['method'])
                @endif
                <button type="submit" 
                        class="{{ $action['class'] ?? 'btn btn-danger table_action_father' }}" 
                        title="{{ $action['title'] ?? '' }}"
                        @if(isset($action['confirm'])) onclick="return confirm('{{ $action['confirm'] }}')" @endif>
                    <i class="{{ $action['icon'] }} table_action_icon"></i>
                </button>
            </form>
        @endif
    @endforeach
</div>
