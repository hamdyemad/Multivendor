@extends('layout.app')

@section('styles')
    @vite(['Modules/CatalogManagement/resources/css/tree-view.css'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                ['title' => trans('catalogmanagement::variantsconfig.tree_view')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::variantsconfig.variants_configurations_tree') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.variants-configurations.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                            <i class="uil uil-list-ul"></i> {{ trans('common.list_view') }}
                        </a>
                        <a href="{{ route('admin.variants-configurations.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                            <i class="uil uil-plus"></i> {{ trans('catalogmanagement::variantsconfig.add_variants_config') }}
                        </a>
                    </div>
                </div>

                {{-- Tree View Container --}}
                <div class="variant-configs-tree">
                    @if($treeData && $treeData->count() > 0)
                        <div class="tree-container">
                            @foreach($treeData as $rootConfig)
                                @include('catalogmanagement::variants-config.partials.tree-node', ['variantsConfig' => $rootConfig, 'level' => 0])
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="uil uil-folder-open" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3">{{ trans('catalogmanagement::variantsconfig.no_variants_configs_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal with Loading Component --}}
<x-delete-with-loading
    modalId="modal-delete-variants-config"
    tableId=""
    deleteButtonClass="delete-variants-config"
    :title="__('main.confirm delete')"
    :message="__('main.are you sure you want to delete this')"
    itemNameId="delete-variants-config-name"
    confirmBtnId="confirmDeleteBtn"
    :cancelText="__('main.cancel')"
    :deleteText="__('main.delete')"
    :loadingDeleting="trans('main.deleting') ?? 'Deleting...'"
    :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'"
    :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'"
    :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'"
    :errorDeleting="__('main.error on delete')"
/>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle tree node expansion
        $('.tree-toggle').on('click', function(e) {
            e.stopPropagation();
            const $toggle = $(this);
            const $node = $toggle.closest('.tree-node');
            const $children = $node.find('> .tree-children');
            
            $toggle.toggleClass('collapsed');
            $children.toggleClass('expanded');
            
            // Update icon
            const icon = $toggle.find('i');
            if ($toggle.hasClass('collapsed')) {
                icon.removeClass('uil-angle-down').addClass('uil-angle-right');
            } else {
                icon.removeClass('uil-angle-right').addClass('uil-angle-down');
            }
        });

        // Expand/collapse on node click (optional)
        $('.tree-node-content').on('click', function(e) {
            if (!$(e.target).closest('.tree-actions').length) {
                $(this).find('.tree-toggle').trigger('click');
            }
        });
    });
</script>
@endpush
