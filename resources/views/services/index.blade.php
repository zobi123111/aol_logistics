@section('title', 'Supplier Services')
{{-- @section('sub-title', 'Supplier Services') --}}
@section('sub-title', __('messages.Supplier Service'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}</a>
        <a href="{{ route('services.create', encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
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
    <table class="table mt-3" id="service">
        <thead>
            <tr>
                <th> {{ __('messages.Origin') }} </th>
                <th> {{ __('messages.Destination') }} </th>
                <th> {{ __('messages.Cost') }} </th>
                <th> {{ __('messages.Actions') }} </th>
            </tr>
        </thead>
        <tbody>
        @if($services->isEmpty())
            <tr>
                <td colspan="5" class="text-center"> {{ __('messages.No suppliers found') }}</td>
            </tr>
            @else
            @foreach($services as $service)
                <tr>
                <td>
                    {{ $service->origindata ? $service->origindata->street . ', ' . $service->origindata->city . ', ' . $service->origindata->state . ', ' . $service->origindata->zip . ', ' . $service->origindata->country : 'N/A' }}
                </td>
                <td>
                    {{ $service->destinationdata ? $service->destinationdata->street . ', ' . $service->destinationdata->city . ', ' . $service->destinationdata->state . ', ' . $service->destinationdata->zip . ', ' . $service->destinationdata->country : 'N/A' }}
                </td>                   
                <td>{{ $service->cost }}</td>
                    <td class="icon-design">
                        <!-- <a href="{{ route('services.edit', [$supplier->id, $service->id]) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('services.destroy', [$supplier->id, $service->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form> -->
                        <a href="{{ route('services.edit', [encode_id($supplier->id), encode_id($service->id)]) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="{{ encode_id($supplier->id) }}" data-service-id="{{ encode_id($service->id) }}"></i>
                    </td>
                </tr>
            @endforeach
    @endif

        </tbody>
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
$(document).ready(function() {
    $('#service').DataTable();
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
});
</script>

@endsection