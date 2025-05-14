@section('title', 'Supplier Services')
{{-- @section('sub-title', 'Supplier Services') --}}
@section('sub-title', __('messages.Supplier Service'). ' | Company: ' . $supplier->company_name)
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}</a>
        <a href="{{ route('supplier_services.create', encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
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
   
    <table class="table table-striped respo_table mt-3" id="supplier-services-table">
        <thead>
            <tr>
                <th>Master Service</th>
                <th>Cost</th>
                <!-- <th>Service Date</th> -->
                <th>Actions</th>
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
    $('#supplier-services-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier_services.index', encode_id($supplier->id)) }}",
        columns: [
            // { data: 'id', name: 'id' },
            { data: 'service_name', name: 'masterService.service_name' },
            { data: 'cost', name: 'cost' },
            // { data: 'service_date', name: 'service_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var supplierId = $(this).data('supplier-id');
        var serviceId = $(this).data('service-id');
        // var username = $(this).closest('tr').find('.username').text();
        var modal_text =
            `{{ __('messages.Are you sure you want to delete ?') }}`;
        $('.delete_content').html(modal_text);
        $('#deleteRoleFormId').attr('action', `/suppliers/${supplierId}/services/${serviceId}`);

        $('#deleteRoleForm').modal('show');
    });

</script>

@endsection