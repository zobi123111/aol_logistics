@section('title', 'Logs')
@section('sub-title', 'Logs')
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
    @if(checkAllowedModule('activity-logs', 'activityLogs.all')->isNotEmpty())
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
    @endif
    @if(checkAllowedModule('activity-logs', 'logs.delete')->isNotEmpty())
    <div>
    <div class="pagetitle">
            <h1>Delete Logs</h1>
        </div>
    <div class="mb-3">
    <select id="date_range" class="form-control">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="custom">Custom</option>
    </select>
</div>

<div id="custom_date_range" style="display: none;">
    <input type="date" id="start_date" class="form-control mb-2">
    <input type="date" id="end_date" class="form-control mb-2">
</div>
<div id="edit_profile_photo_error" class="text-danger otp_error"></div>

<button id="delete_logs" class="btn btn-danger">Delete Logs</button>
</div>
@endif
</div>
<!-- End of Delete Model -->
@endsection

@section('js_scripts')
<script>
$(document).ready(function() {
    $('#logs_table').DataTable();

    $('#date_range').change(function () {
        if ($(this).val() === 'custom') {
            $('#custom_date_range').show();
        } else {
            $('#custom_date_range').hide();
        }
    });

    // Handle log deletion request
    $('#delete_logs').click(function () {
        let dateRange = $('#date_range').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        $.ajax({
            url: "{{ route('logs.delete') }}",
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            data: {
                date_range: dateRange,
                start_date: startDate,
                end_date: endDate
            },
            success: function (response) {
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errorResponse = JSON.parse(xhr.responseText);

                    if (errorResponse.errors && errorResponse.errors.otp) {
                        var otpErrors = errorResponse.errors.otp.join('<br>');
                        $('.otp_error').html(otpErrors);
                    } else if (errorResponse.error) {
                        $('.otp_error').html(errorResponse.error);
                    }
                }
            }
        });
    });
});
</script>

@endsection