@section('title', 'Logs')
{{-- @section('sub-title', 'Logs') --}}
@section('sub-title', GoogleTranslate::trans('Logs', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}

    </div>
    @endif
    @if(checkAllowedModule('activity-logs', 'activityLogs.all')->isNotEmpty())
    <table class="table table-striped" id="logs_table" style="padding-top: 10px;">
        <thead>
            <tr>
                <th scope="col"> {{ GoogleTranslate::trans('User Name', app()->getLocale()) }}  </th>
                <th scope="col"> {{ GoogleTranslate::trans('Log Type', app()->getLocale()) }}  </th>
                <th scope="col"> {{ GoogleTranslate::trans('Description', app()->getLocale()) }}  </th>
                <th scope="col"> {{ GoogleTranslate::trans('Timestamp', app()->getLocale()) }}  </th>
            </tr>
        </thead>
      
    </table>
    @endif
    @if(checkAllowedModule('activity-logs', 'logs.delete')->isNotEmpty())
    <div>
    <div class="pagetitle">
            <h1>{{ GoogleTranslate::trans('Delete Logs', app()->getLocale()) }} </h1>
        </div>
    <div class="mb-3">
    <select id="date_range" class="form-control">
        <option value="daily">{{ GoogleTranslate::trans('Daily', app()->getLocale()) }} </option>
        <option value="weekly">{{ GoogleTranslate::trans('Weekly', app()->getLocale()) }} </option>
        <option value="monthly">{{ GoogleTranslate::trans('Monthly', app()->getLocale()) }} </option>
        <option value="custom">{{ GoogleTranslate::trans('Custom', app()->getLocale()) }} </option>
    </select>
</div>

<div id="custom_date_range" style="display: none;">
    <input type="date" id="start_date" class="form-control mb-2">
    <input type="date" id="end_date" class="form-control mb-2">
</div>
<div id="edit_profile_photo_error" class="text-danger otp_error"></div>

<button id="delete_logs" class="btn btn-danger">{{ GoogleTranslate::trans('Delete Logs', app()->getLocale()) }} </button>
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
                // If data is available, format the date
                if (data) {
                    // Using moment.js or native JavaScript to format date
                    return moment(data).format('YYYY-MM-DD hh:mm A');
                }
                return data; // Return raw data if date is missing
            }
        }

        ],
        language: {
            sSearch: "{{ GoogleTranslate::trans('Search', app()->getLocale()) }}",
            sLengthMenu: "{{ GoogleTranslate::trans('Show', app()->getLocale()) }} _MENU_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            sInfo: "{{ GoogleTranslate::trans('Showing', app()->getLocale()) }} _START_ {{ GoogleTranslate::trans('to', app()->getLocale()) }} _END_ {{ GoogleTranslate::trans('of', app()->getLocale()) }} _TOTAL_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            oPaginate: {
                sPrevious: "{{ GoogleTranslate::trans('Previous', app()->getLocale()) }}",
                sNext: "{{ GoogleTranslate::trans('Next', app()->getLocale()) }}"
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