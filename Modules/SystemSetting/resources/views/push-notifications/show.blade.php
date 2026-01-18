@extends('layout.app')

@section('title', __('systemsetting::push-notification.notification_details'))

@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.notification_details')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::push-notification.notification_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.push-notifications.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3><i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('systemsetting::push-notification.title')" :model="$notification" fieldName="title" :languages="$languages" />
                                            <x-translation-display :label="__('systemsetting::push-notification.description')" :model="$notification" fieldName="description" :languages="$languages" type="html" />
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.type') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @switch($notification->type)
                                                            @case('all')
                                                                <span class="badge badge-info badge-round badge-lg">{{ __('systemsetting::push-notification.type_all') }}</span>
                                                                @break
                                                            @case('specific')
                                                                <span class="badge badge-primary badge-round badge-lg">{{ __('systemsetting::push-notification.type_specific') }}</span>
                                                                @break
                                                            @case('all_vendors')
                                                                <span class="badge badge-success badge-round badge-lg">{{ __('systemsetting::push-notification.type_all_vendors') }}</span>
                                                                @break
                                                            @case('specific_vendors')
                                                                <span class="badge badge-warning badge-round badge-lg">{{ __('systemsetting::push-notification.type_specific_vendors') }}</span>
                                                                @break
                                                        @endswitch
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::push-notification.created_by') }}</label>
                                                    <p class="fs-15 color-dark">{{ $notification->createdBy?->name ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($customersCount > 0)
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-users-alt me-1"></i>{{ __('systemsetting::push-notification.customers') }} ({{ $customersCount }})</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="customersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('common.name') }}</th>
                                                        <th>{{ __('common.email') }}</th>
                                                        <th>{{ __('common.phone') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($vendorsCount > 0)
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-store me-1"></i>{{ __('systemsetting::push-notification.vendors') }} ({{ $vendorsCount }})</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="vendorsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('common.name') }}</th>
                                                        <th>{{ __('common.email') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-eye me-1"></i>{{ __('systemsetting::push-notification.views') }} ({{ $viewsCount }})</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="viewsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('common.name') }}</th>
                                                        <th>{{ __('common.email') }}</th>
                                                        <th>{{ __('systemsetting::push-notification.viewed_at') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $notification->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $notification->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3><i class="uil uil-image me-1"></i>{{ __('systemsetting::push-notification.image') }}</h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($notification->image)
                                            <div class="image-wrapper">
                                                <img src="{{ formatImage($notification->image) }}" alt="{{ __('systemsetting::push-notification.image') }}" class="img-fluid rounded">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ __('common.no_image') ?? 'No image uploaded' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-image-modal />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var dtLanguage = {
                emptyTable: "{{ __('datatable.empty_table') }}",
                processing: "{{ __('datatable.processing') }}",
                info: "{{ __('datatable.info') }}",
                infoEmpty: "{{ __('datatable.info_empty') }}",
                infoFiltered: "{{ __('datatable.info_filtered') }}",
                lengthMenu: "{{ __('datatable.length_menu') }}",
                zeroRecords: "{{ __('datatable.zero_records') }}",
                paginate: {
                    first: "{{ __('datatable.first') }}",
                    last: "{{ __('datatable.last') }}",
                    next: "{{ __('datatable.next') }}",
                    previous: "{{ __('datatable.previous') }}"
                }
            };

            @if($customersCount > 0)
            $('#customersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.system-settings.push-notifications.customers-datatable', ['id' => $notification->id]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'full_name', name: 'full_name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'phone', name: 'phone', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
            @endif

            @if($vendorsCount > 0)
            $('#vendorsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.system-settings.push-notifications.vendors-datatable', ['id' => $notification->id]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
            @endif

            $('#viewsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.system-settings.push-notifications.views-datatable', ['id' => $notification->id]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'viewed_at', name: 'viewed_at', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
        });
    </script>
@endpush
