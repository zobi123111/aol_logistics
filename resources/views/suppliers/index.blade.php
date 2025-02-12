@section('title', 'Supplier')
@section('sub-title', 'Supplier')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    @if(checkAllowedModule('suppliers', 'suppliers.create')->isNotEmpty() )
    <div class="create_btn">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Create Supplier</a>
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
                @if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty() || checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty()|| checkAllowedModule('suppliers', 'suppliers.delete')->isNotEmpty())
                <th scope="col">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if($suppliers->isEmpty())
            <tr>
                <td colspan="8" class="text-center">No suppliers found</td>
            </tr>
            @else
            @foreach($suppliers as $supplier)
            <tr>
                <td class="company">{{ $supplier->company_name }}</td>
                <td>{{ $supplier->dba }}</td>
                <!-- <td>{{ $supplier->street_address }}</td>
                <td>{{ $supplier->city }}</td>
                <td>{{ $supplier->state }}</td>
                <td>{{ $supplier->zip_code }}</td>
                <td>{{ $supplier->country }}</td> -->
                <td>{{ $supplier->office_phone }}</td>
                <td>{{ $supplier->primary_contact_email }}</td>
                <td>{{ $supplier->primary_contact_office_phone }}</td>
                <td>{{ $supplier->primary_contact_mobile_phone }}</td>
                <!-- <td>{{ $supplier->user_role }}</td>
                <td>{{ $supplier->user_email }}</td>
                <td>{{ $supplier->user_office_phone }}</td>
                <td>{{ $supplier->user_mobile_phone }}</td>
                <td>{{ $supplier->service_type }}</td> -->
                <!-- <td>{{ $supplier->currency }}</td>
                <td>{{ $supplier->preferred_language }}</td> -->
                <!-- <td> -->
                <!-- @foreach(json_decode($supplier->documents) as $document) -->
                <!-- <a href="{{ asset('storage/' . $document) }}" target="_blank">View</a><br> -->
                <!-- <img src="{{ asset('storage/' . $document) }}" alt="{{ basename($document) }}" style="max-width: 150px; max-height: 150px; margin-bottom: 10px;"> -->

                <!-- @endforeach -->
                <!-- </td> -->
                <!-- <td>{{ $supplier->scac_number }}</td> -->
                <!-- <td>
                    @foreach(json_decode($supplier->scac_documents) as $document)
                    <a href="{{ asset('storage/' . $document) }}" target="_blank">View</a><br>
                    @endforeach
                </td> -->
                <!-- <td>{{ $supplier->caat_number }}</td>
                <td>
                    @foreach(json_decode($supplier->caat_documents) as $document)
                    <a href="{{ asset('storage/' . $document) }}" target="_blank">View</a><br>
                    @endforeach
                </td> -->
                @if(checkAllowedModule('suppliers', 'suppliers.toggleStatus')->isNotEmpty())
                <td>
                        <!-- Bootstrap switch to toggle status -->
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox"
                            id="flexSwitchCheckChecked{{ encode_id($supplier->id) }}" data-id="{{ encode_id($supplier->id) }}"
                            {{ $supplier->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($supplier->id) }}">
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                </td>
                @endif
                @if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty() || checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty()|| checkAllowedModule('suppliers', 'suppliers.destroy')->isNotEmpty())
                <td>
                @if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty())
                    <a href="{{ route('suppliers.edit', encode_id($supplier->id)) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                    @endif
                    @if(checkAllowedModule('suppliers', 'suppliers.destroy')->isNotEmpty() )
                    <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="{{ encode_id($supplier->id) }}"></i>
                    @endif
                    <!-- View Button to navigate to supplier details -->
                    @if(checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty() )
                    <a href="{{ route('suppliers.show', encode_id($supplier->id)) }}" class=""><i
                            class="fa-solid fa-eye view-icon table_icon_style blue_icon_color"></i></a>
                    @endif
                </td>
                @endif

                
            </tr>
            @endforeach
            @endif
        </tbody>
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
    $('#supplier').DataTable();
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