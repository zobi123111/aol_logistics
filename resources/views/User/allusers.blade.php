@section('title', 'Users')
@section('sub-title', 'Users')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
@if(checkAllowedModule('users', 'save_user.index')->isNotEmpty())
    <div class="create_btn">
        <a href="#" class="btn btn-primary create-button btn_primary_color" id="createUser" data-toggle="modal"
            data-target="#userModal">Create User</a>
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
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                @if(checkAllowedModule('users', 'users.toggleStatus')->isNotEmpty())
                <th scope="col">Status</th>
                @endif
                @if(checkAllowedModule('users', 'user.get')->isNotEmpty())
                <th scope="col">Edit</th>
                @endif
                @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                <th scope="col">Delete</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($users as $val)
            <tr>
                <td scope="row" class="fname">{{ $val->fname }}</td>
                <td scope="row" class="lname">{{ $val->lname }}</td>
                <td>{{ $val->email }}</td>
                <td>{{$val->roledata->role_name}}</td>
                @if(checkAllowedModule('users', 'users.toggleStatus')->isNotEmpty())
                <td>
                    <!-- Bootstrap switch to toggle status -->
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox" 
                                id="flexSwitchCheckChecked{{ $val->id }}" 
                                data-id="{{ $val->id }}" 
                                {{ $val->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckChecked{{ $val->id }}">
                            {{ $val->is_active ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                </td>
                @endif
                @if(checkAllowedModule('users', 'user.get')->isNotEmpty())
                <td><i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"
                        data-user-id="{{ $val->id }}"></i></td>
                @endif
                @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                <td><i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-user-id="{{ $val->id }}"></i></td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Create User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Create Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="Create_user" class="row g-3 needs-validation">
                    @csrf
                    <div class="form-group">
                        <label for="firstname" class="form-label">First Name<span class="text-danger">*</span></label>
                        <input type="text" name="firstname" class="form-control">
                        <div id="firstname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
                        <input type="text" name="lastname" class="form-control">
                        <div id="lastname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control">
                        <div id="email_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control">
                        <div id="password_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword" class="form-label">Confirm Password<span
                                class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" id="confirmpassword">
                        <div id="password_confirmation_error" class="text-danger error_e"></div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                        <select name="role_name" class="form-select" id="role">
                            @foreach($roles as $val)
                            <option value="{{ $val->id }}">{{ $val->role_name }}</option>
                            @endforeach

                        </select>
                        <div id="role_name_error" class="text-danger error_e"></div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" type="button" class="btn btn-secondary btn_secondary_color"
                            data-bs-dismiss="modal">Close</a>
                        <a href="#" type="button" id="saveuser" class="btn btn-primary btn_primary_color sbt_btn">Save
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--End of create user-->

<!-- Edit user -->
<div class="modal fade" id="editUserDataModal" tabindex="-1" role="dialog" aria-labelledby="editUserDataModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserDataModalLabel">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="edit_user" class="row g-3 needs-validation">
                    @csrf
                    <input type="hidden" name="edit_id" value="">
                    <div class="form-group">
                        <label for="firstname" class="form-label">First Name<span class="text-danger">*</span></label>
                        <input type="text" name="fname" class="form-control">
                        <div id="fname_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
                        <input type="text" name="lname" class="form-control">
                        <div id="lname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control">
                        <div id="email_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                        <select name="edit_role_name" class="form-select" id="edit_role">
                            @foreach($roles as $val)
                            <option value="{{ $val->id }}">{{ $val->role_name }}</option>
                            @endforeach

                        </select>
                        <div id="edit_role_name_error" class="text-danger error_e"></div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" type="button" class="btn btn-secondary btn_secondary_color"
                            data-bs-dismiss="modal">Close</a>
                        <a href="#" type="button" id="edituser" class="btn btn-primary btn_primary_color sbt_btn">Update
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--End of Edit user-->

<!--Delete  Modal -->
<form action="{{ url('/users/delete') }}" method="POST">
    @csrf
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                    <input type="hidden" name="id" id="userid" value="">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user "<strong><span id="append_name"> </span></strong>" ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary user_delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>
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

    $('#saveuser').click(function(e) {
        e.preventDefault();
        $('.error_e').html('');
        $.ajax({
            url: '{{ url("/save_user") }}',
            type: 'POST',
            data: $('#Create_user').serialize(),
            success: function(response) {
                $('#userModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                var errorMessage = JSON.parse(xhr.responseText);
                var validationErrors = errorMessage.errors;
                $.each(validationErrors, function(key, value) {
                    var html1 = '<p>' + value + '</p>';
                    $('#' + key + '_error').html(html1);
                });
            }
        });
    });

    $('.edit-user-icon').click(function(e) {
        e.preventDefault();
        $('.error_ee').html('');
        var userId = $(this).data('user-id');
        vdata = {
            id: userId,
            "_token": "{{ csrf_token() }}",
        };
        $.ajax({
            type: 'post',
            url: "{{ url('users/edit') }}",
            data: vdata,
            success: function(response) {
                console.log(response.user.fname);
                $('input[name="fname"]').val(response.user.fname);
                $('input[name="lname"]').val(response.user.lname);
                $('input[name="email"]').val(response.user.email);
                $('input[name="edit_id"]').val(userId);

                // Primary role
                var userRoleId = response.user.role;
                $('#role_id option').removeAttr('selected');
                $('#edit_role option[value="' + userRoleId + '"]').attr('selected',
                    'selected');

                //Secondary role
                var secondary_role = response.user.role_id1;
                //  $('#secondary_role').val('');
                $('#secondary_role option').removeAttr('selected');
                $('#secondary_role option[value="' + secondary_role + '"]').attr('selected',
                    'selected');
                $('#editUserDataModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $('.delete-icon').click(function(e) {
        e.preventDefault();
        $('#deleteUserModal').modal('show');
        var userId = $(this).data('user-id');
        var fname = $(this).closest('tr').find('.fname').text();
        var lname = $(this).closest('tr').find('.lname').text();
        var name = fname + ' ' + lname;
        $('#append_name').html(name);
        $('#userid').val(userId);

    });


    $('#edituser').click(function(e) {
        e.preventDefault();
        $('.error_e').html('');
        $.ajax({
            url: '{{ url("/users/edit/save") }}',
            type: 'POST',
            data: $('#edit_user').serialize(),
            success: function(response) {
                $('#userModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                var errorMessage = JSON.parse(xhr.responseText);
                var validationErrors = errorMessage.errors;
                $.each(validationErrors, function(key, value) {
                    var html1 = '<p>' + value + '</p>';
                    $('#' + key + '_error').html(html1);
                });
            }
        });
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