@section('title', 'Client')
@section('sub-title', 'Client')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('client', 'client.create')->isNotEmpty() )
    <div class="create_btn">
        <a href="{{ route('client.create') }}" class="btn btn-primary create-button btn_primary_color" id="createClient">Create Client</a>
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
    @if(checkAllowedModule('client', 'client.index')->isNotEmpty() )
    <table class="table table-striped" id="client">
        <thead>
            <tr>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                @if(checkAllowedModule('client', 'client.toggleStatus')->isNotEmpty())
                <th>Status</th>
                @endif
                @if(checkAllowedModule('client', 'client.edit')->isNotEmpty() || checkAllowedModule('client', 'client.show')->isNotEmpty()|| checkAllowedModule('client', 'client.delete')->isNotEmpty())
                <th scope="col">Actions</th>
                @endif
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
                <td class="clientFname">{{ $clientdata->fname }}</td>
                <td>{{ $clientdata->lname }}</td>
                <td>{{ $clientdata->email }}</td>
                <td>{{ $clientdata->roledata->role_name }}</td>
                @if(checkAllowedModule('client', 'client.toggleStatus')->isNotEmpty())
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
                @endif
                @if(checkAllowedModule('client', 'client.edit')->isNotEmpty() || checkAllowedModule('client', 'client.show')->isNotEmpty()|| checkAllowedModule('client', 'client.destroy')->isNotEmpty())
                <td>
                @if(checkAllowedModule('client', 'client.edit')->isNotEmpty())
                    <a href="#" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                    @endif
                    @if(checkAllowedModule('client', 'client.destroy')->isNotEmpty() )
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-clientdata-id="{{ encode_id($clientdata->id) }}"></i>
                    @endif
                </td>
                @endif

                
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    @endif
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
        console.log("test");
        var modal_text =
            `Are you sure you want to delete this client?`;

        $('.delete_content').html(modal_text);
        $('#deleteClientFormId').attr('action', '/client/' + clientId);
        $('#deleteClientForm').modal('show');
    });
});
</script>

@endsection