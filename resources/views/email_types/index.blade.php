@section('title',  __('messages.email_notification'))
@section('sub-title', __('messages.email_notification'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.toggle') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($types as $type)
            <tr>
            <td>{{ ucwords(str_replace('_', ' ', $type->name)) }}</td>
            <td>
                    <form action="{{ route('email-types.toggle', $type->id) }}" method="POST" id="toggle-form-{{ $type->id }}">
                        @csrf
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_active"
                                {{ $type->is_active ? 'checked' : '' }}
                                onchange="document.getElementById('toggle-form-{{ $type->id }}').submit();"
                            >
                        </div>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
    </table>
    </div>
@endsection
@section('js_scripts')
<script>

</script>
@endsection