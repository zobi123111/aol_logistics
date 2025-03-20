@section('title', 'Logs')
{{-- @section('sub-title', 'Logs') --}}
@section('sub-title', __('messages.Logs'))
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
    <table class="table table-striped respo_table" id="logs_table" style="padding-top: 10px;">
        <thead>
            <tr>
                <th scope="col"> {{ __('messages.User Name') }}  </th>
                <th scope="col"> {{ __('messages.Log Type') }}  </th>
                <th scope="col"> {{ __('messages.Description') }}  </th>
                <th scope="col"> {{ __('messages.Timestamp') }}  </th>
            </tr>
        </thead>
      
    </table>
    @endif
    @if(checkAllowedModule('activity-logs', 'logs.delete')->isNotEmpty())
    <div>
    <div class="pagetitle">
            <h1>{{ __('messages.Delete Logs') }} </h1>
        </div>
    <div class="mb-3">
    <select id="date_range" class="form-control">
        <option value="daily">{{ __('messages.Daily') }} </option>
        <option value="weekly">{{ __('messages.Weekly') }} </option>
        <option value="monthly">{{ __('messages.Monthly') }} </option>
        <option value="custom">{{ __('messages.Custom') }} </option>
    </select>
</div>

<div id="custom_date_range" style="display: none;">
    <input type="date" id="start_date" class="form-control mb-2">
    <input type="date" id="end_date" class="form-control mb-2">
</div>
<div id="edit_profile_photo_error" class="text-danger otp_error"></div>

<button id="delete_logs" class="btn btn-danger">{{ __('messages.Delete Logs') }} </button>
</div>
@endif
</div>
<!-- End of Delete Model -->
@endsection

@section('js_scripts')
<script>
$(document).ready(function() {
    // $('#logs_table').DataTable();
    $('#logs_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logs.data') }}", // Ensure this route returns JSON
        columns: [
            { data: 'user_name', name: 'user_name' },
            { data: 'log_type', name: 'log_type' },
            { data: 'description', name: 'description' },
            {
            data: 'created_at', 
            name: 'created_at',
            render: function (data, type, row) {
                if (data) {
                    let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone; 
                    return moment(data).tz(userTimezone).format('YYYY-MM-DD hh:mm A'); 
                }
                return data; 
            }
        }

        ],
        language: {
            sSearch: "{{ __('messages.Search') }}",
            sLengthMenu: "{{ __('messages.Show') }} _MENU_ {{ __('messages.entries') }}",
            sInfo: "{{ __('messages.Showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
            oPaginate: {
                sPrevious: "{{ __('messages.Previous') }}",
                sNext: "{{ __('messages.Next') }}"
            }
        }
    });

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