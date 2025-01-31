@section('title', 'Roles')
@section('sub-title', 'Roles')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('roles', 'roles.create')->isNotEmpty())
    <div class="create_btn">
        <a href="{{ route('roles.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Create Role</a>
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
    <table class="table table-striped" id="user_table" style="padding-top: 10px;">
        <thead>
            <tr>
                <th scope="col">Role</th>
                <th scope="col">User Type</th>
                @if(checkAllowedModule('roles', 'roles.edit')->isNotEmpty())
                <th scope="col">Edit</th>
                @endif
                @if(checkAllowedModule('roles', 'roles.destroy')->isNotEmpty())
                <th scope="col">Delete</th>
                @endif
            </tr>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>

            <tr>
                <td scope="row" class="fname">{{ $role->role_name }}</td>
                <td>{{$role->userType->name}}</td>
                @if(checkAllowedModule('roles', 'roles.edit')->isNotEmpty())
                <td><a href="{{ route('roles.edit', ['role' => encode_id($role->id)]) }}"><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"
                            data-user-id="{{ $role->id }}"></i></a></td>
                @endif
                @if(checkAllowedModule('roles', 'roles.destroy')->isNotEmpty())

                <td>
                    <form action="{{ route('roles.destroy', ['role' => encode_id($role->id)]) }}" method="POST"
                        style="display: inline;"
                        onsubmit="return confirm('Are you sure you want to delete this role and all its permissions?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border: none; background: none; cursor: pointer;">
                            <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"></i>
                        </button>
                    </form>
                </td>
                @endif
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
    $('#user_table').DataTable();

    $('#createUser').on('click', function() {
        $('.error_e').html('');
        $('.alert-danger').css('display', 'none');
        $('#userModal').modal('show');
    });


});
</script>

@endsection