@section('title', 'Roles')
{{-- @section('sub-title', 'Roles') --}}
@section('sub-title', __('messages.Role'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">

    @if(checkAllowedModule('roles', 'roles.create')->isNotEmpty() && Auth::user()->is_dev)
    <div class="create_btn">
        <a href="{{ route('roles.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Create Role') }} </a>
    </div>
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
    <table class="table table-striped" id="role_table" style="padding-top: 10px;">
        <thead>
            <tr>
                <th scope="col"> {{ __('messages.Role') }} </th>
                <th scope="col"> {{ __('messages.User Type') }} </th>
                @if((checkAllowedModule('roles', 'roles.edit')->isNotEmpty() || checkAllowedModule('roles', 'roles.destroy')->isNotEmpty()) || (Auth::user()->is_dev ||Auth::user()->is_owner))
                <th scope="col"> {{ __('messages.Actions') }} </th>
                @endif
                <!-- @if(checkAllowedModule('roles', 'roles.destroy')->isNotEmpty() && Auth::user()->is_dev)
                <th scope="col">Delete</th>
                @endif -->
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td scope="row" class="fname">{{ $role->role_name }}</td>
                <td>{{$role->userType->name}}</td>

                @if((checkAllowedModule('roles', 'roles.edit')->isNotEmpty() || checkAllowedModule('roles', 'roles.destroy')->isNotEmpty()) || (Auth::user()->is_dev || Auth::user()->is_owner))
                <td>
                @if(checkAllowedModule('roles', 'roles.edit')->isNotEmpty() || (Auth::user()->is_dev ||Auth::user()->is_owner))
                <a href="{{ route('roles.edit', ['role' => encode_id($role->id)]) }}"><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"
                            data-user-id="{{ $role->id }}"></i></a>
                @endif
                @if(Auth::user()->is_dev)
               <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                data-role-id="{{ encode_id($role->id) }}" data-user-count="{{$role->users_count}}" ></i>
                @endif
                </td>

                @endif
            </tr>
            @endforeach
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
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">{{ __('messages.Close') }} </button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color">{{ __('messages.Delete') }} </button>
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
    $('#role_table').DataTable();
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var roleId = $(this).data('role-id');
        var usercountId = $(this).data('user-count');
        var fname = $(this).closest('tr').find('.fname').text();
        var modal_text =
            `You can't delete "<strong><span id="append_name">${fname} </span></strong>" role because it is assigned to "<strong><span id="append_name">${usercountId} </span></strong>" users.`;
        $(".role_delete").prop("disabled", true)
        if (usercountId < 1) {
            var modal_text =
                // `Are you sure you want to delete this role "<strong><span id="append_name">${fname} </span></strong>" ?`;
                `{{ __('messages.Are you sure you want to delete this role') }} "<strong><span id="append_name">${fname} </span></strong>" ?`;
            $(".role_delete").prop("disabled", false)

        } else {}

        $('.delete_content').html(modal_text);
        // $('#append_name').html(fname);
        $('#deleteRoleFormId').attr('action', '/roles/' + roleId);
        $('#deleteRoleForm').modal('show');

    });


});
</script>

@endsection