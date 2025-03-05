@section('title', 'Supplier Equipment')
{{-- @section('sub-title', 'Supplier Equipment') --}}
@section('sub-title', __('messages.Supplier Equipment'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
        <a href="{{ route('supplier_units.create', encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add Equipment') }}  </a>
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
    <table class="table mt-3 respo_table" id="unit">
        <thead>
            <tr>
                <th> {{ __('messages.Unit Type') }} </th>
                <th> {{ __('messages.Unit Number') }} </th>
                <th> {{ __('messages.License Plate') }} </th>
                <th> {{ __('messages.State') }} </th>
                <th> {{ __('messages.Actions') }} </th>
            </tr>
        </thead>
        <tbody>
        @if($units->isEmpty())
            <tr>
                <td colspan="5" class="text-center">{{ __('messages.No suppliers found') }}</td>
            </tr>
            @else
            @foreach ($units as $unit)
                <tr>
                    <td>{{ $unit->unit_type }}</td>
                    <td>{{ $unit->unit_number }}</td>
                    <td>{{ $unit->license_plate }}</td>
                    <td>{{ $unit->state }}</td>
                    <!-- <td>
                        <a href="{{ route('supplier_units.edit', [$supplier->id, $unit->id]) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('supplier_units.destroy', [$supplier->id, $unit->id]) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        @if($unit->trashed())
                            <form action="{{ route('supplier_units.restore', [$supplier->id, $unit->id]) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success">Restore</button>
                            </form>
                        @endif
                    </td> -->
                    <td class="icon-design">
                    <a href="{{ route('supplier_units.edit', [encode_id($supplier->id), encode_id($unit->id)]) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="{{ encode_id($supplier->id) }}" data-unit-id="{{ encode_id($unit->id) }}"></i>
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
    $('#unit').DataTable();
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var supplierId = $(this).data('supplier-id');
        var unitId = $(this).data('unit-id');
        // var username = $(this).closest('tr').find('.username').text();
        var modal_text =
            `{{ __('messages.Are you sure you want to delete ?') }}`;
        $('.delete_content').html(modal_text);
        $('#deleteRoleFormId').attr('action', `/suppliers/${supplierId}/units/${unitId}`);

        $('#deleteRoleForm').modal('show');
    });
});
</script>

@endsection