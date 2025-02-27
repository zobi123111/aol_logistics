@section('title', 'Client')
{{-- @section('sub-title', 'Client') --}}
@section('sub-title', GoogleTranslate::trans('Client', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('client', 'client.create')->isNotEmpty() )
    <div class="create_btn">
        <a href="{{ route('client.create') }}" class="btn btn-primary create-button btn_primary_color" id="createClient"> {{ GoogleTranslate::trans('Create Client', app()->getLocale()) }} </a>
    </div>
    @endif
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}
    </div>
    @endif
    @if(checkAllowedModule('client', 'client.index')->isNotEmpty() )
    <table class="table table-striped" id="client">
        <thead>
            <tr>
                <th scope="col"> {{ GoogleTranslate::trans('First Name', app()->getLocale()) }} </th>
                <th scope="col"> {{ GoogleTranslate::trans('Last Name', app()->getLocale()) }} </th>
                <th scope="col"> {{ GoogleTranslate::trans('Business Name', app()->getLocale()) }} </th>
                <th scope="col"> {{ GoogleTranslate::trans('Email', app()->getLocale()) }} </th>
                <th scope="col"> {{ GoogleTranslate::trans('Role', app()->getLocale()) }} </th>
                @if(checkAllowedModule('client', 'client.toggleStatus')->isNotEmpty() )
                <th scope="col"> {{ GoogleTranslate::trans('Status', app()->getLocale()) }} </th>
                @endif
                @if(checkAllowedModule('client', 'client.edit')->isNotEmpty() ||  checkAllowedModule('client', 'client.destroy')->isNotEmpty())
                <th scope="col"> {{ GoogleTranslate::trans('Actions', app()->getLocale()) }} </th>
                @endif
                <th scope="col"> {{ GoogleTranslate::trans('Users', app()->getLocale()) }} </th>
            </tr>
        </thead>
       
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
                        <h5 class="modal-title" id="exampleModalLabel"> {{ GoogleTranslate::trans('Delete', app()->getLocale()) }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ GoogleTranslate::trans('Close', app()->getLocale()) }} Close</button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color"> {{ GoogleTranslate::trans('Delete', app()->getLocale()) }} </button>
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
    $('#client').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('client.index') }}", 
        columns: [
            { data: 'fname', name: 'fname' },
            { data: 'lname', name: 'lname' },
            { data: 'business_name', name: 'business_name' },
            { data: 'email', name: 'email' },
            { data: 'role_name', name: 'role_name' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'client_users', name: 'client_users', orderable: false, searchable: false }
        ],
        language: {
            sSearch: "{{ GoogleTranslate::trans('Search', app()->getLocale()) }}",
            sLengthMenu: "{{ GoogleTranslate::trans('Show', app()->getLocale()) }} _MENU_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            sInfo: "{{ GoogleTranslate::trans('Showing', app()->getLocale()) }} _START_ {{ GoogleTranslate::trans('to', app()->getLocale()) }} _END_ {{ GoogleTranslate::trans('of', app()->getLocale()) }} _TOTAL_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            oPaginate: {
                sPrevious: "{{ GoogleTranslate::trans('Previous', app()->getLocale()) }}",
                sNext: "{{ GoogleTranslate::trans('Next', app()->getLocale()) }}"
            }
        },
        columnDefs: [
            {
                targets: 0, 
                className: "fname" 
            },
            {
                targets: 1, 
                className: "lname" 
            },
            {
            targets: 6, 
            className: 'icon-design',
           
        }
        ]
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var clientId = $(this).data('clientdata-id');
        var fname = $(this).closest('tr').find('.fname').text();
        var lname = $(this).closest('tr').find('.lname').text();
        var modal_text =
            `{{ GoogleTranslate::trans('Are you sure you want to delete this client', app()->getLocale()) }} "<strong><span id="append_name">${fname} ${lname}</span></strong>"?`;

        $('.delete_content').html(modal_text);
        $('#deleteClientFormId').attr('action', '/client/' + clientId);
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