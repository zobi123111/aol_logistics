@section('title', 'Assign Load')
{{-- @section('sub-title', 'Assign Load') --}}
@section('sub-title', GoogleTranslate::trans('Assign Load', app()->getLocale()))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }} </a>
    </div>
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

        <!-- <table class="table mt-3" id="assign_loads">
    <thead>
        <tr>
            <th> {{ GoogleTranslate::trans('Supplier Company Name', app()->getLocale()) }} </th>
            <th> {{ GoogleTranslate::trans('Service Details', app()->getLocale()) }} </th>
            <th> {{ GoogleTranslate::trans('Cost', app()->getLocale()) }} </th>
            <th> {{ GoogleTranslate::trans('Action', app()->getLocale()) }} </th>
        </tr>
    </thead>
    <tbody>
    @if($suppliers->isEmpty())
        <tr>
            <td colspan="4" class="text-center"> {{ GoogleTranslate::trans('No Service found', app()->getLocale()) }} </td>
        </tr>
        @else
        @foreach ($suppliers as $supplier)
            @foreach ($supplier->services as $service)
                <tr>
                    <td>{{ $supplier->company_name }}</td>
                    <td>
                    {{ $service->origindata ? $service->origindata->street . ', ' . $service->origindata->city . ', ' . $service->origindata->state . ', ' . $service->origindata->zip . ', ' . $service->origindata->country : 'N/A' }}
                    →  {{ $service->destinationdata ? $service->destinationdata->street . ', ' . $service->destinationdata->city . ', ' . $service->destinationdata->state . ', ' . $service->destinationdata->zip . ', ' . $service->destinationdata->country : 'N/A' }}

                    </td>
                    <td>${{ number_format($service->cost, 2) }}</td>
                    <td>
                    @if($load->supplier_id == $supplier->id)
                 <span class="badge bg-success"> {{ GoogleTranslate::trans('Assigned', app()->getLocale()) }} </span>
                @else
                    <a href="{{ route('loads.assign.supplier', ['load_id' => encode_id($load->id), 'supplier_id' => encode_id($supplier->id), 'service_id' => encode_id($service->id)]) }}" class="btn btn-primary create-button btn_primary_color">
                        {{ GoogleTranslate::trans('Assign', app()->getLocale()) }} 
                    </a>
                @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        @endif
    </tbody>
</table> -->
<div class="card mt-3">
    <div class="card-header blue_icon_color">
        Load Details
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>AOL Number:</strong> {{ $load->aol_number }}</p>
                <p><strong>Origin:</strong> 
                    {{ $load->origindata ? $load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->zip . ', ' . $load->origindata->country : 'N/A' }}
                </p>
                <p><strong>Destination:</strong> 
                    {{ $load->destinationdata ? $load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->zip . ', ' . $load->destinationdata->country : 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Weight:</strong> {{ $load->weight ?? 'N/A' }} kg</p>
                <p><strong>Status:</strong> 
                    <span class="badge 
                        {{ $load->status == 'Pending' ? 'bg-warning' : ($load->status == 'Completed' ? 'bg-success' : 'bg-secondary') }}">
                        {{ $load->status }}
                    </span>
                </p>
                <p><strong>Created At:</strong> {{ $load->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<h3 class=" ">Assigned Services</h3>
<table class="table" id="assignedServices">
    <thead>
        <tr>
            <th>Supplier Company Name</th>
            <th>Service Details</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        <!-- Show Assigned Services at the Top -->
        @if($assignedServices->isEmpty())
            <tr>
                <td colspan="4" class="text-center">No Assigned Services</td>
            </tr>
        @else
            @foreach ($assignedServices as $assigned)
                <tr>
                    <td>{{ $assigned->supplier->company_name }}</td>
                    <td>
                        {{ $assigned->service->origindata ? $assigned->service->origindata->street . ', ' . $assigned->service->origindata->city . ', ' . $assigned->service->origindata->state . ', ' . $assigned->service->origindata->zip . ', ' . $assigned->service->origindata->country : 'N/A' }}
                        →  
                        {{ $assigned->service->destinationdata ? $assigned->service->destinationdata->street . ', ' . $assigned->service->destinationdata->city . ', ' . $assigned->service->destinationdata->state . ', ' . $assigned->service->destinationdata->zip . ', ' . $assigned->service->destinationdata->country : 'N/A' }}
                    </td>
                    <td>${{ number_format($assigned->service->cost, 2) }}</td>
                    <td>
                        <!-- <form action="{{ route('unassign.service', $assigned->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> 
                            </button>
                        </form> -->
                        <button type="button" class="btn btn-danger" onclick="showDeleteModal({{ $assigned->id }})">
        <i class="fas fa-times"></i>
    </button>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>


<h3 class="mt-3">Services</h3>
<table class="table" id="allServices">
    <thead>
        <tr>
        <th>Supplier Company Name</th>
            <th>Service Details</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($suppliers as $supplier)
        @foreach ($supplier->services as $service)
            <tr>
                <td>{{ $supplier->company_name }}</td>
                <td>
                    {{ $service->origindata ? $service->origindata->street . ', ' . $service->origindata->city . ', ' . $service->origindata->state . ', ' . $service->origindata->zip . ', ' . $service->origindata->country : 'N/A' }}
                    →
                    {{ $service->destinationdata ? $service->destinationdata->street . ', ' . $service->destinationdata->city . ', ' . $service->destinationdata->state . ', ' . $service->destinationdata->zip . ', ' . $service->destinationdata->country : 'N/A' }}
                </td>
                <td>${{ number_format($service->cost, 2) }}</td>
                <td>
                    <form action="{{ route('assign.service') }}" method="POST">
                        @csrf
                        <input type="hidden" name="load_id" value="{{ $load->id }}">
                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color">
                            <i class="fas fa-plus"></i> 
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    @endforeach
        @foreach ($remainingSuppliers as $supplier)
            @foreach ($supplier->services as $serviceother)
                <tr>
                    <td>{{ $supplier->company_name }}</td>
                    <td>
                        {{ $serviceother->origindata ? $serviceother->origindata->street . ', ' . $serviceother->origindata->city . ', ' . $serviceother->origindata->state . ', ' . $serviceother->origindata->zip . ', ' . $serviceother->origindata->country : 'N/A' }}
                        →  
                        {{ $serviceother->destinationdata ? $serviceother->destinationdata->street . ', ' . $serviceother->destinationdata->city . ', ' . $serviceother->destinationdata->state . ', ' . $serviceother->destinationdata->zip . ', ' . $serviceother->destinationdata->country : 'N/A' }}
                    </td>
                    <td>${{ number_format($serviceother->cost, 2) }}</td>
                    <td>
                        <form action="{{ route('assign.service') }}" method="POST">
                            @csrf
                            <input type="hidden" name="load_id" value="{{ $load->id }}">
                            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                            <input type="hidden" name="service_id" value="{{ $serviceother->id }}">
                            <button type="submit" class="btn btn-primary role_delete btn_primary_color">
                                <i class="fas fa-plus"></i> 
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
</tbody>

</table>
<form method="POST" id="deleteRoleFormId">
    @csrf
    @method('DELETE')

    <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Unassignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to unassign this service?</p>

                    <!-- Reason for Unassigning -->
                    <div class="mb-3">
                        <label for="unassign_reason" class="form-label">Reason for Unassigning</label>
                        <select class="form-select" name="unassign_reason" id="unassign_reason" onchange="toggleOtherReason()">
                            <option value="Client Requested">Client Requested</option>
                            <option value="Unable to Offer Service">Unable to Offer Service</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Other Reason Input (Hidden by Default) -->
                    <div class="mb-3 d-none" id="other_reason_container">
                        <label for="other_reason" class="form-label">Please Specify</label>
                        <input type="text" class="form-control" name="other_reason" id="other_reason">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Unassign</button>
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
    $('#assignedServices').DataTable()
    $('#allServices').DataTable()
});

function showDeleteModal(assignedId) {
    let form = document.getElementById("deleteRoleFormId");
    form.action = `/unassign-service/${assignedId}`; // Update form action dynamically
    new bootstrap.Modal(document.getElementById('deleteRoleForm')).show();
}

// Show input field if "Other" is selected
function toggleOtherReason() {
    let reasonSelect = document.getElementById('unassign_reason');
    let otherReasonContainer = document.getElementById('other_reason_container');

    if (reasonSelect.value === "Other") {
        otherReasonContainer.classList.remove('d-none');
    } else {
        otherReasonContainer.classList.add('d-none');
    }
}
</script>

@endsection