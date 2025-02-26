@extends('layout.app')

@section('title', 'Load Details')
@section('sub-title', 'Load')

@section('content')
<div class="main_cont_outer">
<div class="create_btn mb-3">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser"><i class="bi bi-arrow-left-circle-fill"> </i>back</a>
    </div>
    <div class="container">
    <h2 class="mb-4">Load Details</h2>

    <!-- Load Information Card -->
    <div class="card mb-4">
        <div class="card-header blue_icon_color">
            Load Information
        </div>
        <div class="card-body">
            <p><strong>AOL Number:</strong> {{ $load->aol_number }}</p>
            <p><strong>Origin:</strong> {{ $load->origindata
                    ? $load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country
                    : 'N/A' }}</p>
            <p><strong>Destination:</strong> {{ $load->destinationdata
                    ? $load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country
                    : 'N/A' }}</p>
            <p><strong>Service Type:</strong> {{ $load->service_type }}</p>
            <p><strong>Payer:</strong> {{ $load->payer }}</p>
            <p><strong>Equipment Type:</strong> {{ $load->equipment_type }}</p>
            <p><strong>Trailer Number:</strong> {{ $load->trailer_number ?? 'N/A' }}</p>
            <p><strong>Port of Entry:</strong> {{ $load->port_of_entry ?? 'N/A' }}</p>
            <p><strong>Supplier:</strong> {{ $load->supplier ? $load->supplier->company_name : 'N/A' }}</p>
            <p><strong>Weight:</strong> {{ $load->weight ?? 'N/A' }} kg</p>
            <p><strong>Delivery Deadline:</strong> {{ $load->delivery_deadline->format('d M Y') }}</p>
            <p><strong>Customer PO:</strong> {{ $load->customer_po ?? 'N/A' }}</p>
            <p><strong>Hazmat:</strong> {!! $load->is_hazmat ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>
            <p><strong>Inbond:</strong> {!! $load->is_inbond ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>
            <p><strong>Status:</strong> 
                <span class="badge 
                    {{ $load->status == 'Pending' ? 'bg-warning' : ($load->status == 'Completed' ? 'bg-success' : 'bg-secondary') }}">
                    {{ $load->status }}
                </span>
            </p>
            <p><strong>Created At:</strong> {{ $load->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <!-- Assigned Services Table -->
    <h3>Assigned Services</h3>
    <table class="table table-bordered" id="assigned">
        <thead class="bg-secondary text-white">
            <tr>
                <th>Supplier</th>
                <th>Service Details</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            @forelse($load->assignedServices as $assignedService)
                <tr>
                    <td>{{ $assignedService->supplier->company_name }}</td>
                    <td>
                    {{ $assignedService->service->origindata ? $assignedService->service->origindata->street . ', ' . $assignedService->service->origindata->city . ', ' . $assignedService->service->origindata->state . ', ' . $assignedService->service->origindata->zip . ', ' . $assignedService->service->origindata->country : 'N/A' }} 
                    →  
                    {{ $assignedService->service->destinationdata ? $assignedService->service->destinationdata->street . ', ' . $assignedService->service->destinationdata->city . ', ' . $assignedService->service->destinationdata->state . ', ' . $assignedService->service->destinationdata->zip . ', ' . $assignedService->service->destinationdata->country : 'N/A' }}

                    </td>
                    <td>${{ number_format($assignedService->service->cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No assigned services</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

@endsection
@section('js_scripts')
<script>
    $(document).ready(function() {
    $('#assigned').DataTable();
    });

</script>
@endsection