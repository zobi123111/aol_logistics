@section('title', __('messages.Assign Service'))
@section('sub-title', __('messages.Assign Service'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}</a>
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

    @if(session()->has('error'))
    <div class="alert alert-danger fade show" role="alert">
        <i class="bi bi-x-circle me-1"></i>
        {{ session()->get('error') }}
    </div>
    @endif

        <!-- <table class="table mt-3" id="assign_loads">
    <thead>
        <tr>
            <th> Supplier Company Name</th>
            <th> Service Details </th>
            <th> Cost</th>
            <th> Action</th>
        </tr>
    </thead>
    <tbody>
    @if($suppliers->isEmpty())
        <tr>
            <td colspan="4" class="text-center"> No Service found </td>
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
                 <span class="badge bg-success"> Assigned </span>
                @else
                    <a href="{{ route('loads.assign.supplier', ['load_id' => encode_id($load->id), 'supplier_id' => encode_id($supplier->id), 'service_id' => encode_id($service->id)]) }}" class="btn btn-primary create-button btn_primary_color">
                        Assign
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
        {{ __('messages.Load Details') }} 
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>{{ __('messages.AOL Number') }} :</strong> {{ $load->aol_number }}</p>
                <p><strong>{{ __('messages.Client') }} :</strong> {{ $load->creatorfor->business_name }}</p>
                <p><strong>{{ __('messages.Origin') }} :</strong> 
                {{  $load->origindata
                    ? ($load->origindata->name ?: ($load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country))
                    : 'N/A'; }}
                </p>
                <p><strong>{{ __('messages.Destination') }} :</strong> 
                {{$load->destinationdata
                    ? ($load->destinationdata->name ?: ($load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country))
                    : 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>{{ __('messages.Weight') }} :</strong> {{ $load->weight !== null ? number_format($load->weight, 2, '.', ',') . ' lbs' : 'N/A' }} </p>
                <p><strong>{{ __('messages.Status') }} :</strong> 
                    <span class="badge 
                        {{ $load->status == 'Pending' ? 'bg-warning' : ($load->status == 'Completed' ? 'bg-success' : 'bg-secondary') }}">
                        {{ $load->status }}
                    </span>
                </p>
                <p><strong>{{ __('messages.Created At') }} :</strong> {{ $load->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
<h3 class="mt-3 services-text">{{ __('messages.Services') }} </h3>
<div class="d-flex justify-content-start assign-service" style="column-gap: 10px;">
<div class="form-group mb-3">
    <label for="supplierFilter" class="form-label">{{ __('messages.Search by Supplier') }} </label>
<select id="supplierFilter" class="form-control select2 mr-3">
    <option value="">All Suppliers</option>
    @foreach ($allSuppliers as $supplier)
        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
    @endforeach
</select>
</div>
<div class="form-group mb-3">

<label for="ServicesFilter" class="form-label">{{ __('messages.Search by Service') }} </label>
<select id="ServicesFilter" class="form-control ml-2">
    <option value="">All Services</option>
    <option value="">Select Service Type</option>
        <option value="freight">Freight</option>
        <option value="warehouse">Warehouse</option>
</select>
</div>
</div>
<input type="hidden" id="loadId" value="{{ encode_id($load->id) }}">
<table class="table" id="allServices">
    <thead>
        <tr>
            <th>{{ __('messages.Supplier Company Name') }} </th>
            <!-- <th>{{ __('messages.supplier_transport_type') }}</th> -->
            <th>{{ __('messages.service_type') }}</th>
            <th> {{ __('messages.Service Name') }}  </th>
            <th>{{ __('messages.Service Details') }} </th>
             @if(isAolAdminUser())
            <th>{{ __('messages.Cost') }} </th>
            @endif
            <th>{{ __('messages.Action') }} </th>
        </tr>
    </thead>
    <tbody>
    @if($suppliers->isEmpty())
            <tr>
                <td colspan="7" class="text-center">No Services</td>
            </tr> 
        @else
    @foreach ($suppliers as $supplier)
        @foreach ($supplier->supplierServices as $service)
            <tr>
                <td>{{ $supplier->dba }}</td>
            <!-- <td>{{ ucfirst($supplier->service_type) }}</td> -->
                <td>{{ ucfirst($service->masterService->service_type) }}</td>
                <td>{{ $service->masterService->service_name ?? 'NA' }}</td>

                <td>

                    @if ($service->masterService->service_type === 'warehouse')
                      
                     {{$service->masterService->street . ', ' . $service->masterService->city . ', ' . $service->masterService->state . ', ' . $service->masterService->zip . ', ' . $service->masterService->country}}

                      @else
                    {{ $service->masterService->origindata 
                        ? ($service->masterService->origindata->name 
                            ?: ($service->masterService->origindata->street . ', ' . $service->masterService->origindata->city . ', ' . $service->masterService->origindata->state . ', ' . $service->masterService->origindata->zip . ', ' . $service->masterService->origindata->country)) 
                        : 'N/A' }}  
                    →  
                    {{ $service->masterService->destinationdata 
                        ? ($service->masterService->destinationdata->name 
                            ?: ($service->masterService->destinationdata->street . ', ' . $service->masterService->destinationdata->city . ', ' . $service->masterService->destinationdata->state . ', ' . $service->masterService->destinationdata->zip . ', ' . $service->masterService->destinationdata->country)) 
                        : 'N/A' }}
                      @endif
                </td>
                 @if(isAolAdminUser())
                <td>{{ optional($service->clientServices->first())->cost !== null ? '$' . number_format($service->clientServices->first()->cost, 2) : '---' }}
                @endif

                </td>
                <td>
                    <!-- <form action="{{ route('assign.service') }}" method="POST">
                        @csrf
                        <input type="hidden" name="load_id" value="{{ $load->id }}">
                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color">
                            <i class="fas fa-plus"></i> 
                        </button>
                    </form> -->
                    <button type="button" 
                        class="btn btn-primary btn_primary_color open-modal-btn" 
                        data-load_id="{{ $load->id }}"
                        data-supplier_id="{{ $supplier->id }}"
                        data-supplier_service_id="{{ $service->id }}"
                        data-service_id="{{ $service->masterService->id }}">
                        <i class="fas fa-plus"></i> 
                    </button>
                </td>
            </tr>
        @endforeach
    @endforeach
       @endif
</tbody>

</table>
<h3 class="services-text">{{ __('messages.Assigned Services') }} </h3> 
<table class="table" id="assignedServices">
    <thead>
        <tr>
            <th>{{ __('messages.Supplier Company Name') }} </th>
            <!-- <th>{{ __('messages.supplier_transport_type') }}</th> -->
            <th>{{ __('messages.service_type') }}</th>
             <th> {{ __('messages.Service Name') }}  </th>
            <th>{{ __('messages.quantity') }}</th>
            <th>{{ __('messages.Service Details') }} </th>
             @if(isAolAdminUser())
            <th>{{ __('messages.Cost') }} </th>
            @endif
            <th>{{ __('messages.Action') }} </th>
        </tr>
    </thead>
    <tbody>

        <!-- Show Assigned Services at the Top -->
        @if($assignedServices->isEmpty())
            <tr>
                <td colspan="8" class="text-center">{{ __('messages.No Assigned Services') }} </td>
            </tr>
        @else
            @foreach ($assignedServices as $assigned)
                <tr>
                    <td>{{ $assigned->supplier->dba }}</td>
                    <!-- <td>{{ ucfirst($assigned->supplier->service_type) }}</td> -->
                    <td>{{ ucfirst($assigned->service->masterService->service_type) }}</td>
                    <td>{{ $assigned->service->masterService->service_name?? 'NA' }}</td>
                    <td>{{ $assigned->quantity }}</td>
                    <td>
                    @if ($assigned->service->masterService->service_type === 'warehouse')
                      
                    {{$assigned->service->masterService->street . ', ' . $assigned->service->masterService->city . ', ' . $assigned->service->masterService->state . ', ' . $assigned->service->masterService->zip . ', ' . $assigned->service->masterService->country}}

                        @else
                        {{ $assigned->service->masterService->origindata 
                        ? ($assigned->service->masterService->origindata->name 
                            ?: ($assigned->service->masterService->origindata->street . ', ' . $assigned->service->masterService->origindata->city . ', ' . $assigned->service->masterService->origindata->state . ', ' . $assigned->service->masterService->origindata->zip . ', ' . $assigned->service->masterService->origindata->country)) 
                        : 'N/A' }}  
                    →  
                    {{ $assigned->service->masterService->destinationdata 
                        ? ($assigned->service->masterService->destinationdata->name 
                            ?: ($assigned->service->masterService->destinationdata->street . ', ' . $assigned->service->masterService->destinationdata->city . ', ' . $assigned->service->masterService->destinationdata->state . ', ' . $assigned->service->masterService->destinationdata->zip . ', ' . $assigned->service->masterService->destinationdata->country)) 
                        : 'N/A' }}
                        @endif
                    </td>
                     @if(isAolAdminUser())
                    <td>
                        ${{ number_format(($assigned->cost ?? $assigned->cost) * $assigned->quantity, 2) }}  
                        @if($assigned->quantity > 1)
                            <br>
                            <small class="text-muted">(${{ number_format($assigned->cost ?? $assigned->cost, 2) }} per unit)</small>
                        @endif
                    </td>
                    @endif
                    <td>
                        <button type="button" class="btn btn-danger" onclick="showDeleteModal({{ $assigned->id }})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>

<h3 class="mt-3 services-text">{{ __('messages.Canceled Assigned Services') }}</h3>
<table class="table" id="assignedServices">
    <thead>
        <tr>
            <th>{{ __('messages.Supplier Company Name') }} </th>
             <!-- <th>{{ __('messages.supplier_transport_type') }}</th> -->
            <th>{{ __('messages.service_type') }}</th>
            <th> {{ __('messages.Service Name') }}  </th>
            <th>{{ __('messages.service_details') }}</th>
             @if(isAolAdminUser())
            <th>{{ __('messages.Cost') }} </th> 
             @endif
            <th>{{ __('messages.reason_of_cancellation') }}</th>
        </tr>
    </thead>
    <tbody>

        <!-- Show Assigned Services at the Top -->
        @if($deletedAssignedServices->isEmpty())
            <tr>
                <td colspan="7" class="text-center">{{ __('messages.No Canceled Services') }}</td>
            </tr>
        @else
            @foreach ($deletedAssignedServices as $assigned)
                <tr>
                    <td>{{ $assigned->supplier->dba }}</td>
                    <!-- <td>{{ ucfirst($assigned->supplier->service_type) }}</td> -->

                    <td>{{ ucfirst($assigned->service->masterService->service_type) }}</td>
                    <td>{{ $assigned->service->masterService->service_name?? 'NA' }}</td>

                    <td>
                        @if ($assigned->service->masterService->service_type === 'warehouse')
                      
                      {{$assigned->service->masterService->street . ', ' . $assigned->service->masterService->city . ', ' . $assigned->service->masterService->state . ', ' . $assigned->service->masterService->zip . ', ' . $assigned->service->masterService->country}}
 
                       @else

                     {{ $assigned->service->masterService->origindata 
                    ? ($assigned->service->masterService->origindata->name 
                        ?: ($assigned->service->masterService->origindata->street . ', ' . $assigned->service->masterService->origindata->city . ', ' . $assigned->service->masterService->origindata->state . ', ' . $assigned->service->masterService->origindata->zip . ', ' . $assigned->service->masterService->origindata->country)) 
                    : 'N/A' }}  
                →  
                {{ $assigned->service->masterService->destinationdata 
                    ? ($assigned->service->masterService->destinationdata->name 
                        ?: ($assigned->service->masterService->destinationdata->street . ', ' . $assigned->service->masterService->destinationdata->city . ', ' . $assigned->service->masterService->destinationdata->state . ', ' . $assigned->service->masterService->destinationdata->zip . ', ' . $assigned->service->masterService->destinationdata->country)) 
                    : 'N/A' }}

                       @endif
                    </td>
                     @if(isAolAdminUser())
                    <td>${{ number_format($assigned->cost ?? $assigned->cost, 2) }}</td>
                    @endif
                    <td>{{ $assigned->cancel_reason }}</td>

                </tr>
            @endforeach
        @endif

    </tbody>
</table>


<form method="POST" id="deleteRoleFormId">
    @csrf
    @method('DELETE')

    <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('messages.confirm_unassignment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('messages.are_you_sure_unassign') }}</p>

                    <!-- Reason for Unassigning -->
                    <div class="mb-3">
                        <label for="unassign_reason" class="form-label">{{ __('messages.reason_for_unassigning') }}</label>
                        <select class="form-select" name="unassign_reason" id="unassign_reason" onchange="toggleOtherReason()">
                            <option value="Client Requested">{{ __('messages.client_requested') }}</option>
                            <option value="Unable to Offer Service">{{ __('messages.unable_to_offer_service') }}</option>
                            <option value="Other">{{ __('messages.other') }}</option>
                        </select>
                    </div>

                    <!-- Other Reason Input (Hidden by Default) -->
                    <div class="mb-3 d-none" id="other_reason_container">
                        <label for="other_reason" class="form-label">{{ __('messages.please_specify') }}</label>
                        <input type="text" class="form-control" name="other_reason" id="other_reason">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('messages.unassign') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="assignServiceModalN" tabindex="-1" aria-labelledby="assignServiceModalNLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignServiceModalNLabel">{{ __('messages.assign_service') }}</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('assign.service') }}" method="POST">
                    @csrf
                    <input type="hidden" name="load_id" id="modal_load_id">
                    <input type="hidden" name="supplier_id" id="modal_supplier_id">
                    <input type="hidden" name="service_id" id="modal_service_id">
                    <input type="hidden" name="supplier_service_id" id="modal_supplier_service_id">
                    
                    <div class="form-group">
                        <label for="quantity" class="form-label">{{ __('messages.quantity') }}</label>
                        <input type="number" class="form-control" name="quantity" value="1" id="modal_quantity">
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-primary btn_primary_color">{{ __('messages.assign') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js_scripts')

<script>
$(document).ready(function() {
    // $('#assignedServices').DataTable()
    // $('#allServices').DataTable()

  
    $('#supplierFilter').select2({
            placeholder: "{{ __('messages.Select a Supplier') }}",
            allowClear: true,
            width: '100%'
    });

    $('#ServicesFilter').select2({
        placeholder: "{{ __('messages.Select a Service') }}",
        allowClear: true,
        width: '100%'
    });

    // $('#supplierFilter').on('change', function () {
    //     var supplierId = $(this).val();
    //     var loadId = $('#loadId').val();

    //     $.ajax({
    //         url: '/loads/' + loadId + '/assign',
    //         type: 'GET',
    //         data: { supplier_id: supplierId },
    //         success: function (data) {
    //             $('#allServices tbody').html($(data).find('#allServices tbody').html());
    //         },
    //         error: function () {
    //             console.error("Failed to filter services.");
    //         }
    //     });
    // });
    function filterServices() {
    var supplierId = $('#supplierFilter').val();
    var serviceType = $('#ServicesFilter').val();
    var loadId = $('#loadId').val();

    $.ajax({
        url: '/loads/' + loadId + '/assign',
        type: 'GET',
        data: { 
            supplier_id: supplierId,
            service_type: serviceType 
        },
        success: function (data) {
            // $('#allServices tbody').html($(data).find('#allServices tbody').html());
            var content = $(data).find('#allServices tbody').html().trim();
            
            if (content === '') {
                $('#allServices tbody').html('<tr><td colspan="7" class="text-center">No Services</td></tr>');
            } else {
                $('#allServices tbody').html(content);
            }
        },
        error: function () {
            console.error("Failed to filter services.");
        }
    });
}

// Event listeners for both filters
$('#supplierFilter, #ServicesFilter').on('change', function () {
    filterServices();
});

 
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
$(document).on('click', '.open-modal-btn', function () {
    let loadId = $(this).data('load_id');
    let supplierId = $(this).data('supplier_id');
    let serviceId = $(this).data('service_id');
    let supplierserviceId = $(this).data('supplier_service_id');
    
    // Set modal input values
    $('#modal_load_id').val(loadId);
    $('#modal_supplier_id').val(supplierId);
    $('#modal_service_id').val(serviceId);
    $('#modal_supplier_service_id').val(supplierserviceId);

    // Manually open the modal
    $('#assignServiceModalN').modal('show');
});
</script>

@endsection