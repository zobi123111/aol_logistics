@section('title', 'Client')
 <!-- @section('sub-title', 'Client') -->
@section('sub-title', __('messages.Client'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('client', 'client.create')->isNotEmpty() )
    <div class="create_btn">
        <a href="{{ route('client.create') }}" class="btn btn-primary create-button btn_primary_color" id="createClient"> 
            {{ __('messages.Create Client') }}
        </a>
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
    <table class="table table-striped respo_table" id="client">
        <thead>
            <tr>
                <!-- <th scope="col"> {{ __('messages.First Name') }} </th>
                <th scope="col"> {{ __('messages.Last Name') }} </th> -->
                <th scope="col"> {{ __('messages.Business Name') }} </th>
                <!-- <th scope="col"> {{ __('messages.Email') }} </th> -->
                <!-- <th scope="col"> {{ __('messages.Role') }} </th> -->
                @if(checkAllowedModule('client', 'client.toggleStatus')->isNotEmpty() )
                <th scope="col"> {{ __('messages.Status') }} </th>
                @endif
                @if(checkAllowedModule('client', 'client.edit')->isNotEmpty() ||  checkAllowedModule('client', 'client.destroy')->isNotEmpty())
                <th scope="col"> {{ __('messages.Actions') }} </th>
                @endif
                <th scope="col"> {{ __('messages.Users') }} </th>
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
                        <h5 class="modal-title" id="exampleModalLabel"> {{ __('messages.Delete') }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ __('messages.Close') }} Close</button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color"> {{ __('messages.Delete') }} </button>
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
            // { data: 'fname', name: 'fname' },
            // { data: 'lname', name: 'lname' },
            { data: 'business_name', name: 'business_name' },
            // { data: 'email', name: 'email' },
            // { data: 'role_name', name: 'role_name' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'client_users', name: 'client_users', orderable: false, searchable: false }
        ],
        language: {
            sSearch: "{{ __('messages.Search') }}",
            sLengthMenu: "{{ __('messages.Show') }} _MENU_ {{ __('messages.entries') }}",
            sInfo: "{{ __('messages.Showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
            oPaginate: {
                sPrevious: "{{ __('messages.Previous') }}",
                sNext: "{{ __('messages.Next') }}"
            }
        },
        columnDefs: [
            // {
            //     targets: 0, 
            //     className: "fname" 
            // },
            // {
            //     targets: 1, 
            //     className: "lname" 
            // },
            {
            targets: 2, 
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
            `{{ __('messages.Are you sure you want to delete this client') }} "<strong><span id="append_name">${fname} ${lname}</span></strong>"?`;

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