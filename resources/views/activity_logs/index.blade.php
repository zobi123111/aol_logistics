@section('title', 'Logs')
@section('sub-title', 'Logs')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('roles', 'roles.create')->isNotEmpty())
    <!-- <div class="create_btn">
        <a href="{{ route('roles.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Create Role</a>
    </div> -->
    @endif
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif
    <table class="table table-striped" id="logs_table" style="padding-top: 10px;">
        <thead>
            <tr>
                <th scope="col">User Name</th>
                <th scope="col">Log Type</th>
                <th scope="col">Description</th>
                <th scope="col">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)

            <tr>
                <td scope="row" class="fname">{{ $log->user->fname }} {{ $log->user->lname }}</td>
                <td scope="row">{{ $log->log_type }}</td>
                <td scope="row">{{ $log->description }}</td>
                <td scope="row">{{ $log->created_at->format('Y-m-d h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- End of Delete Model -->
@endsection

@section('js_scripts')
<script>
$(document).ready(function() {
    $('#logs_table').DataTable();
});
</script>

@endsection