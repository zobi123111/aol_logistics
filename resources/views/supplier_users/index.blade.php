@section('title', 'Supplier Users')
@section('sub-title', 'Supplier Users')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> back</a>
        <a href="{{ route('supplier_users.create', encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Create Supplier User</a>
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
    @php
    $supplierId = request()->route('supplierId'); 
 
@endphp

<input type="hidden" id="supplierId" value="{{ $supplierId }}">
    <table class="table table-striped mt-3" id="supplierUser">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <!-- <th scope="col">Email</th> -->
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <!-- <tbody>
        @if($users->isEmpty())
            <tr>
                <td colspan="5" class="text-center">No suppliers found</td>
            </tr>
            @else
            @foreach($users as $user)
            <tr>
                <td class="username">{{ $user->fname }} {{ $user->lname }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $user->roledata->role_name)) }}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox"
                            id="flexSwitchCheckChecked{{ encode_id($user->id) }}" data-id="{{ encode_id($user->id) }}"
                            {{ $user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($user->id) }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                </td>
                <td>
                    <a href="{{ route('supplier_users.edit', [encode_id($supplier->id), encode_id($user->id)]) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="{{ encode_id($supplier->id) }}" data-user-id="{{ encode_id($user->id) }}"></i>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody> -->
    </table>
    <form method="POST" id="deleteRoleFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color">Delete</button>
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
    let supplierId = $("#supplierId").val(); 

    $('#supplierUser').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/suppliers/${supplierId}/users`, 
            type: "GET",
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'role', name: 'role' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        columnDefs: [
            {
                targets: 0, 
                className: "username" 
            }
        ]
        
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var supplierId = $(this).data('supplier-id');
        var userId = $(this).data('user-id');
        var username = $(this).closest('tr').find('.username').text();
        var modal_text =
            `Are you sure you want to delete  "<strong><span id="append_name">${username}</span></strong>"?`;
        $('.delete_content').html(modal_text);
        $('#deleteRoleFormId').attr('action', `/suppliers/${supplierId}/users/${userId}`);

        $('#deleteRoleForm').modal('show');
    });
});
$(document).on('change', '.status-toggle', function() {
    const toggleSwitch = $(this);
    var userId = $(this).data('id');
    var isActive = $(this).prop('checked') ? 1 : 0;

    $.ajax({
        url: '{{ route("users.toggleStatus") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            is_active: isActive
        },
        success: function(response) {
            if (response.success) {
                const label = toggleSwitch.siblings("label");
                label.text(isActive ? "Active" : "Inactive");
                $('#successMessagea').text(response.message).fadeIn().delay(3000).fadeOut();

            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred while updating the user status.');
        }
    });
});
</script>

@endsection