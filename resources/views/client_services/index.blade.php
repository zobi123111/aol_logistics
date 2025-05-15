@section('title', 'client_service')
@section('sub-title', __('messages.client_service') . ' | Client: ' . $client->business_name)
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('client.index') }}" class="btn btn-primary create-button btn_primary_color"
        id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}</a>
        <a href="{{ route('client_services.create', encode_id($client->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add Service') }} </a>
    </div>
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif
   
    <table class="table table-striped respo_table mt-3" id="client-services-table">
        <thead>
            <tr>
            <th>{{ __('messages.master_service') }}</th>
            <th>{{ __('messages.cost') }}</th>
            <th>{{ __('messages.effective_date') }}e</th>
            <th>{{ __('messages.future_cost') }}</th>
            <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
    </table>
    <form method="POST" id="deleteRoleFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{ __('messages.Delete') }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ __('messages.Close') }} </button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color"> {{ __('messages.Delete') }} </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- End of Delete Model -->
@endsection

@section('js_scripts')

<script>
$(document).ready(function () {
    let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;    
    $('#client-services-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('client_services.index', encode_id($client->id)) }}",
        columns: [
            { data: 'service_name', name: 'masterService.service_name' },
            { data: 'cost', name: 'cost' },
            { data: 'service_date', name: 'service_date', render: function(data) { 
                    return data ? moment(data).tz(userTimezone).format('MMM. D, YYYY') : '-'; 
                }  },
            { data: 'schedule_cost', name: 'schedule_cost', render: function(data) { 
        return data ? data : '-'; 
    } },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
    
$(document).on('click', '.delete-icon', function(e) {
    e.preventDefault();
    var clientId = $(this).data('client-id');
    var serviceId = $(this).data('service-id');
    var modal_text = `{{ __('messages.Are you sure you want to delete ?') }}`;
    $('.delete_content').html(modal_text);
    $('#deleteRoleFormId').attr('action', `/clients/${clientId}/services/${serviceId}`);
    $('#deleteRoleForm').modal('show');
});
</script>

@endsection
