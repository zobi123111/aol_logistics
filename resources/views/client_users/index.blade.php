@section('title', 'Client Customer Service Executve')
@section('sub-title', 'Client Customer Service Executve')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('client.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> back</a>
        <a href="{{ route('client_users.create', $id ) }}" class="btn btn-primary create-button btn_primary_color" id="createClient">Create</a>
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
    <table class="table table-striped" id="client">
        <thead>
            <tr>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if($clients->isEmpty())
            <tr>
                <td colspan="8" class="text-center">No clients found</td>
            </tr>
            @else
            @foreach($clients as $clientdata)
            <tr>
                <td class="fname">{{ $clientdata->fname }}</td>
                <td class="lname">{{ $clientdata->lname }}</td>
                <td>{{ $clientdata->email }}</td>
                <td>{{ $clientdata->roledata->role_name }}</td>   
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox"
                            id="flexSwitchCheckChecked{{ encode_id($clientdata->id) }}" data-id="{{ encode_id($clientdata->id) }}"
                            {{ $clientdata->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($clientdata->id) }}">
                            {{ $clientdata->is_active ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                </td>   
                 <td>
                <a href="{{ route('client_users.edit', [encode_id($clientdata->id), $id]) }}" class=""><i
                        class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>


                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                    data-clientdata-id="{{ encode_id($clientdata->id) }}" data-clientmaster-id="{{ $id }}"></i>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

    <form method="POST" id="deleteClientFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteClientForm" tabindex="-1" aria-labelledby="exampleModalLabel"
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
@endsection
@section('js_scripts')

<script>
$(document).ready(function() {
    $('#client').DataTable();
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var clientId = $(this).data('clientdata-id');
        var clientmaster = $(this).data('clientmaster-id');
        var fname = $(this).closest('tr').find('.fname').text();
        var lname = $(this).closest('tr').find('.lname').text();
        var modal_text =
            `Are you sure you want to delete this client "<strong><span id="append_name">${fname} ${lname}</span></strong>"?`;

        $('.delete_content').html(modal_text);
        $('#deleteClientFormId').attr('action', `/clients/${clientId}/users/${clientmaster}`);

        $('#deleteClientForm').modal('show');
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