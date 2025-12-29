<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.recent_activities') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.employee') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.activity') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.activity_date') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities ?? [] as $index => $activity)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($activity->user)
                                @if($activity->user->image)
                                    <img class="rounded-circle" src="{{ asset('storage/' . $activity->user->image) }}" alt="employee" style="width: 40px; height: 40px;">
                                @else
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="uil uil-user text-muted"></i>
                                    </div>
                                @endif
                                <span class="ms-3">{{ $activity->user->getTranslation('name', app()->getLocale()) ?? $activity->user->email }}</span>
                            @else
                                <span class="text-muted">{{ trans('common.system') }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $actionClass = match($activity->action) {
                                    'created' => 'text-success',
                                    'updated' => 'text-primary',
                                    'deleted' => 'text-danger',
                                    'restored' => 'text-info',
                                    default => 'text-secondary'
                                };
                            @endphp
                            <span class="{{ $actionClass }} fw-medium">{{ $activity->translated_description }}</span>
                        </td>
                        <td>{{ $activity->created_at ? $activity->created_at->format('M d, Y h:i A') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ trans('common.no_data_available') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
