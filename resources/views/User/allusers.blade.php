@section('title', 'Users')
@section('sub-title', __('messages.Users'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('users', 'save_user.index')->isNotEmpty())
    <div class="create_btn">
        <a href="#" class="btn btn-primary create-button btn_primary_color" id="createUser" data-toggle="modal"
            data-target="#userModal"> {{ __('messages.Create User') }}</a>
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
    <form method="POST" action="{{ route('users.bulkAction') }}" id="bulk_action_form">
        @csrf
        @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty() || checkAllowedModule('users',
        'users.toggleStatus')->isNotEmpty())
        <div class="d-flex justify-content-start mb-3 bulk_div">
            <!-- Bulk Action Dropdown -->
            <select name="bulk_action" class="form-control w-25" required>
                <option value=""> {{ __('messages.Select Bulk Action') }} </option>
                @if(checkAllowedModule('users', 'user.toggleStatus')->isNotEmpty())
                <option value="change_status">{{ __('messages.Change Status') }}</option>
                @endif
                @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                <option value="delete">{{ __('messages.Delete') }}</option>
                @endif
            </select>

            <!-- Change Status Dropdown for selected users -->
            <select name="status" class="form-control w-25 ml-3" id="status_dropdown" style="display: none;">
                <option value="active"> {{ __('messages.Activate') }} </option>
                <option value="deactivated"> {{ __('messages.Deactivate') }} </option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary create-button btn_primary_color">{{ __('messages.Apply Action') }} </button>
            <div id="bulk_error" class="text-danger error_e"></div>
        </div>
        @endif
        <table class="table table-striped" id="user_table" style="padding-top: 10px;">
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="select_all"></th>
                    <th scope="col"> {{ __('messages.First Name') }}</th>
                    <th scope="col"> {{ __('messages.Last Name') }} </th>
                    <th scope="col"> {{ __('messages.Email') }} </th>
                    <th scope="col"> {{ __('messages.Role') }}</th>
                    <th scope="col"> {{ __('messages.Last Login At') }}</th>
                    @if(checkAllowedModule('users', 'users.toggleStatus')->isNotEmpty())
                        <th scope="col"> {{ __('messages.Status') }} </th>
                    @endif

                    @if(checkAllowedModule('users', 'user.get')->isNotEmpty() || checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                    <th scope="col"> {{ __('messages.Actions') }} </th>
                    @endif
                    <!-- @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                    <th scope="col">Delete</th>
                    @endif -->
                </tr>
            </thead>
            <tbody>
                @if($users->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">{{ __('messages.No users found') }} </td>
                </tr>
                @else
                @foreach($users as $val)
                <tr>
                    <td><input type="checkbox" name="selected_users[]" value="{{ $val->id }}" class="user_checkbox">
                    </td>
                    <td scope="row" class="fname">{{ $val->fname }}</td>
                    <td scope="row" class="lname">{{ $val->lname }}</td>
                    <td>{{ $val->email }}</td>
                    <td>{{$val->roledata->role_name}}</td>
                    <td>{{ $val->last_login_at ? $val->last_login_at : '--' }}</td>
                    @if(checkAllowedModule('users', 'users.toggleStatus')->isNotEmpty())
                    <td>
                        <!-- Bootstrap switch to toggle status -->
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle" type="checkbox"
                                id="flexSwitchCheckChecked{{ encode_id($val->id) }}" data-id="{{ encode_id($val->id) }}"
                                {{ $val->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($val->id) }}">
                                {{ $val->is_active ? 'Active' : 'Inactive' }}
                            </label>
                        </div>
                    </td>
                    @endif
                    @if(checkAllowedModule('users', 'user.get')->isNotEmpty() || checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                    <td>
                    @if(checkAllowedModule('users', 'user.get')->isNotEmpty())
                    <i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"
                            data-user-id="{{ encode_id($val->id) }}"></i>
                    @endif
                    @if(checkAllowedModule('users', 'user.destroy')->isNotEmpty())
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                            data-user-id="{{ encode_id($val->id) }}"></i>
                    @endif
                    </td>
                    @endif
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </form>
</div>
<!-- Create User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel"> {{ __('messages.Create User') }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="Create_user" class="row g-3 needs-validation"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="firstname" class="form-label"> {{ __('messages.First Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" class="form-control">
                        <div id="firstname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="form-label"> {{ __('messages.Last Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" class="form-control">
                        <div id="lastname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label"> {{ __('messages.Email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control">
                        <div id="email_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label"> {{ __('messages.Password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control">
                        <div id="password_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword" class="form-label"> {{ __('messages.Confirm Password') }} <span
                                class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" id="confirmpassword">
                        <div id="password_confirmation_error" class="text-danger error_e"></div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                        <select name="role_name" class="form-select" id="role">
                            @foreach($roles as $val)
                            <option value="{{ $val->id }}">{{$val->userType->name}} - {{ $val->role_name }}</option>
                            @endforeach

                        </select>
                        <div id="role_name_error" class="text-danger error_e"></div>
                    </div> -->

                    <!-- <div class="form-group">
                        <label for="user_type" class="form-label">User Type<span class="text-danger">*</span></label>
                        <select name="user_type" class="form-select" id="user_type">
                            <option value="">Select User Type</option>
                            @foreach($userType as $type)
                            <option value="{{ encode_id($type->id) }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div> -->

                    <div class="form-group mt-3">
                        <label for="role" class="form-label"> {{ __('messages.Role') }} <span class="text-danger">*</span></label>
                        <select name="role_name" class="form-select" id="role">
                            <option value=""> {{ __('messages.Select Role') }} </option>
                            @foreach($roles as $role)
                            <option value="{{ encode_id($role->id) }}"
                                data-user-type="{{ encode_id($role->user_type_id) }}">
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>
                        <div id="role_name_error" class="text-danger error_e"></div>
                    </div>

                    <!-- Profile Photo Upload -->
                    <div class="form-group mt-3">
                        <label for="profile_photo" class="form-label"> {{ __('messages.Profile Photo') }} </label>
                        <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                        <div id="profile_photo_error" class="text-danger error_e"></div>
                    </div>


                    <div class="modal-footer">
                        <a href="#" type="button" class="btn btn-secondary btn_secondary_color"
                            data-bs-dismiss="modal"> {{ __('messages.Close') }} </a>
                        <a href="#" type="button" id="saveuser" class="btn btn-primary btn_primary_color sbt_btn"><span
                                class="spinner-border spinner-border-sm show_loader noactive" role="status"
                                aria-hidden="true"></span><span> {{ __('messages.Save') }} </span>
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
                <h5 class="modal-title" id="editUserDataModalLabel">{{ __('messages.Edit Employee') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="edit_user" class="row g-3 needs-validation"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" value="">
                    <div class="form-group">
                        <label for="firstname" class="form-label"> {{ __('messages.First Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="fname" class="form-control">
                        <div id="fname_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="form-label"> {{ __('messages.Last Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="lname" class="form-control">
                        <div id="lname_error" class="text-danger error_e"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label"> {{ __('messages.Email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control">
                        <div id="email_error" class="text-danger error_e"></div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="edit_user_type" class="form-label">User Type</label>
                        <select name="edit_user_type" class="form-select" id="edit_user_type">
                            <option value="">Select User Type</option>
                            @foreach($userType as $type)
                            <option value="{{ encode_id($type->id) }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div> -->

                    <div class="form-group mt-3">
                        <label for="edit_role_name" class="form-label"> {{ __('messages.Role') }} <span class="text-danger">*</span></label>
                        <select name="edit_role_name" class="form-select" id="edit_role">
                            <option value=""> {{ __('messages.Select Role Type') }} </option>
                            @foreach($roles as $val)
                            <option value="{{ encode_id($val->id) }}"
                                data-user-type="{{ encode_id($val->user_type_id) }}">{{ $val->role_name }}</option>
                            @endforeach

                        </select>
                        <div id="edit_role_name_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group">
                        <div id="current-profile-photo" class="mt-2">
                        </div>
                        <label for="edit_profile_photo" class="form-label mt-2"> {{ __('messages.Profile Photo') }}</label>
                        <input type="file" name="edit_profile_photo" class="form-control">
                        <input type="hidden" name="remove_profile_photo" id="remove_profile_photo" value="0">
                        <div id="edit_profile_photo_error" class="text-danger error_e"></div>
                    </div>

                    <!-- Password Fields (Only for Super Admin) -->
                    @if(isAdminUser())
                    <div class="form-group mt-3">
                        <label for="edit_password" class="form-label"> {{ __('messages.New Password') }} </label>
                        <input type="password" name="edit_password" class="form-control">
                        <div id="edit_password_error" class="text-danger error_e"></div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="edit_password_confirmation" class="form-label"> {{ __('messages.Confirm New Password') }} </label>
                        <input type="password" name="edit_password_confirmation" class="form-control">
                        <div id="edit_password_confirmation_error" class="text-danger error_e"></div>
                    </div>
                    @endif
                    <div class="modal-footer">
                        <a href="#" type="button" class="btn btn-secondary btn_secondary_color"
                            data-bs-dismiss="modal"> {{ __('messages.Close') }} </a>
                        <a href="#" type="button" id="edituser" class="btn btn-primary btn_primary_color sbt_btn"> {{ __('messages.Update') }} 
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
    @method('POST')
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('messages.Delete') }} </h5>
                    <input type="hidden" name="id" id="userid" value="">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('messages.Are you sure you want to delete this user') }} "<strong><span id="append_name"> </span></strong>" ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ __('messages.Close') }} </button>
                    <button type="submit" class="btn btn-primary user_delete btn_primary_color"> {{ __('messages.Delete') }} </button>
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

        let $btn = $(this);
        if ($btn.hasClass('disabled')) return;
        $('.error_e').html('');
        var formData = new FormData(document.getElementById('Create_user'));
        $.ajax({
            url: '{{ url("/save_user") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                // Show "Saving ..." only before submission
                $btn.addClass('disabled').css("pointer-events", "none");
                $btn.find("span").text("Saving ...");

            },
            success: function(response) {
                $btn.find("span").text("Saved!");
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
                // Revert button text and enable it
                $btn.removeClass('disabled').css("pointer-events", "auto");
                $btn.find("span").text('Save');
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
                $('input[name="fname"]').val(response.user.fname);
                $('input[name="lname"]').val(response.user.lname);
                $('input[name="email"]').val(response.user.email);
                $('input[name="edit_id"]').val(userId);

                // let decodedRoleId = decodeId(response.user.role);    
                // $('#edit_role').val(response.selected_role).trigger('change');
                // $('#edit_user_type').val(response.selected_user_type).trigger('change');
                $('#edit_role option[value="' + response.selected_role + '"]').prop(
                    'selected', true);
                $('#edit_user_type option[value="' + response.selected_user_type + '"]')
                    .prop('selected', true);

                // Handle Profile Photo
                if (response.user.profile_photo) {
                    // Display the profile photo
                    $('#current-profile-photo').html(
                        '<div class="image-cont" style="position:relative;"><label for="edit_profile_photo" class="form-label mt-2"> @json(__('messages.Current Profile Photo')) </label><br><img src="/storage/' +
                        response.user.profile_photo +
                        '" width="100" height="100" class="rounded-circle" alt="Profile Photo"><button type="button" class="btn btn-danger btn-sm position-absolute remove-photo">x</button></div>'
                    );

                    $(".remove-photo").click(function() {
                        if (confirm(
                                "Are you sure you want to remove this profile photo?"
                            )) {
                            // Hide the image preview and remove button
                            $("#current-profile-photo").html("");

                            // Set hidden input value to indicate removal
                            $("#remove_profile_photo").val("1");
                        }
                    });
                } else {
                    // Display a message if no profile photo
                    $('#current-profile-photo').html('<p>No profile photo uploaded.</p>');
                }
                // Primary role
                var userRoleId = response.user.role;
                $('#role_id option').removeAttr('selected');
                $('#edit_role option[value="' + userRoleId + '"]').attr('selected',
                    'selected');

                // //Secondary role
                var secondary_role = response.user.role_id1;
                $('#secondary_role').val('');
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
        var formData = new FormData(document.getElementById('edit_user'));
        $.ajax({
            url: '{{ url("/users/edit/save") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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

    var roleDropdown = $("#role");

    // Store all role options on page load
    var allRoles = roleDropdown.find("option[data-user-type]").clone();

    $("#user_type").on("change", function() {
        var selectedUserType = $(this).val();

        roleDropdown.prop("disabled", true).empty().append(
            '<option value="">Select Role</option>');

        if (selectedUserType) {
            var matchingRoles = allRoles.filter("[data-user-type='" + selectedUserType +
                "']");

            if (matchingRoles.length > 0) {
                roleDropdown.append(matchingRoles);
                roleDropdown.prop("disabled", false);
            }
        }
    });

    $('#edit_user_type').change(function() {
        var selectedUserType = $(this).val();
        $('#edit_role').val('');
        if (selectedUserType === "") {
            $('#edit_role').val('');
            $('#edit_role option').show();
        } else {
            $('#edit_role option').each(function() {
                var userType = $(this).data('user-type');
                if (userType == selectedUserType) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Select/Deselect All checkboxes
    $('#select_all').on('change', function() {
        $('.user_checkbox').prop('checked', this.checked);
    });

    // Show/hide status dropdown based on bulk action selection
    $('select[name="bulk_action"]').on('change', function() {
        const action = $(this).val();
        if (action === 'change_status') {
            $('#status_dropdown').show();
        } else {
            $('#status_dropdown').hide();
        }
    });

    // Form submission validation: ensure at least one user is selected
    $('#bulk_action_form').on('submit', function(event) {
        const selectedUsers = $('.user_checkbox:checked');
        if (selectedUsers.length === 0) {
            $("#bulk_error").html('Please select at least one user.');
            event.preventDefault();
        }
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