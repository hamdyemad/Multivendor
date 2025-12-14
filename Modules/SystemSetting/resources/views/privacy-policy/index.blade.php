@extends('layout.app')

@section('title', __('systemsetting::privacy-policy.privacy_policy'))

@section('content')
    @php
        // Redirect to form view
        return redirect()->route('admin.system-settings.privacy-policy.index');
    @endphp
@endsection
