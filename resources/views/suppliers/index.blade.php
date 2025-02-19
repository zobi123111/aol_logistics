@section('title', 'Supplier')
@section('sub-title', 'Supplier')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Create Supplier</a>
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
    @if(checkAllowedModule('suppliers', 'suppliers.index')->isNotEmpty() )
    <table class="table table-striped" id="supplier">
        <thead>
            <tr>
                <th scope="col">Company Name</th>
                <th scope="col">DBA</th>
                <!-- <th scope="col">Street Address</th>
                <th scope="col">City</th>
                <th scope="col">State</th>
                <th scope="col">Zip Code</th>
                <th scope="col">Country</th> -->
                <th scope="col">Office Phone</th>
                <th scope="col">Email</th>
                <th scope="col">Office Phone</th>
                <th scope="col">Mobile Phone</th>
                <!-- <th scope="col">User Role</th>
                <th scope="col">User Email</th>
                <th scope="col">User Office Phone</th>
                <th scope="col">User Mobile Phone</th>
                <th scope="col">Service Type</th> -->
                <!-- <th scope="col">Currency</th>
                <th scope="col">Preferred Language</th>
                <th scope="col">Documents</th>
                <th scope="col">SCAC Number</th>
                <th scope="col">SCAC Documents</th>
                <th scope="col">CAAT Number</th>
                <th scope="col">CAAT Documents</th> -->
                @if(checkAllowedModule('suppliers', 'suppliers.toggleStatus')->isNotEmpty())
                <th>Status</th>
                @endif
                @if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty() || checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty()|| checkAllowedModule('suppliers', 'suppliers.destroy')->isNotEmpty())
                <th scope="col">Actions</th>
                @endif
                <th scope="col">User</th>
                <th scope="col">Equipment</th>
                <th scope="col">Service</th>
            </tr>
        </thead>
      
    </table>
    @endif
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
    $('#supplier').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('suppliers.index') }}", 
        columns: [
            { data: 'company_name', name: 'company_name' },
            { data: 'dba', name: 'dba' },
            { data: 'office_phone', name: 'office_phone' },
            { data: 'primary_contact_email', name: 'primary_contact_email' },
            { data: 'primary_contact_office_phone', name: 'primary_contact_office_phone' },
            { data: 'primary_contact_mobile_phone', name: 'primary_contact_mobile_phone' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'supplier_users', name: 'supplier_users', orderable: false, searchable: false },
            { data: 'supplier_units', name: 'supplier_units', orderable: false, searchable: false },
            { data: 'services', name: 'services', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: 0, 
                className: "company" 
            }
        ]
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var supplierId = $(this).data('supplier-id');
        var company = $(this).closest('tr').find('.company').text();
        var modal_text =
            `Are you sure you want to delete this supplier from "<strong><span id="append_name">${company}</span></strong>"?`;

        $('.delete_content').html(modal_text);
        $('#deleteRoleFormId').attr('action', '/suppliers/' + supplierId);
        $('#deleteRoleForm').modal('show');
    });
});
$(document).on('change', '.status-toggle', function() {
    const toggleSwitch = $(this);
    var userId = $(this).data('id');
    var isActive = $(this).prop('checked') ? 1 : 0;

    $.ajax({
        url: '{{ route("suppliers.toggleStatus") }}',
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