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
<div class="container">
<div class="card mb-4">
        <div class="card-body text">
            <div class="card-header blue_icon_color">
                Current Tariler Position 
            </div>
            <div class="mb-3 mt-3 address-container" id="address">
                <div id="append_address" class="address-box"></div>
                <div id="append_error" class="text-danger error-message"></div>
            </div>
            <!-- <div id="map"></div> -->
                        <iframe 
            id="mapFrame"
            width="800" 
            height="500" 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0" 
            src=""
            style="display: none;"
            >
            </iframe>
            <br />
            <small>
            <a id="mapLink" 
                href="#" 
                style="color:#0000FF;text-align:left; display: none;" 
                target="_blank">
                See map bigger
            </a>
            </small>
        </div>
    </div>
</div>

@endsection
@section('js_scripts')
<script>
    // $(document).ready(function() {
    // $('#assigned').DataTable();
    // });

    $(document).ready(function() {
        $('#append_address').html('');
        $('#append_error').html('');

        var trailerId = `{{ $load->trailer_number }}`;
        console.log('Selected Trailer ID:', trailerId);

        if (!trailerId) {
            $('#append_error').html('No trailer number found.');
            return;
        }

        var apiUrl = `https://gemco.forzatrans.app/api/Trailers/lastposition/${trailerId}`;
        var apiKey = 'pT7#f9@Lk2^bWz8!xQeV3$Mn6*ArYt1&JdF4+Gh9%UzXo7=KpL';

        $.ajax({
            url: apiUrl,
            type: 'GET',
            beforeSend: function() {
                $('#trailer_no').prop('disabled', true);
                $('#mapFrame, #mapLink').hide();
            },
            headers: {
                'X-API-Key': apiKey,
                'Content-Type': 'application/json'
            },
            timeout: 30000,
            success: function(response) {
                if (!response.length) {
                    $('#trailer_no').prop('disabled', false);
                    $('#append_address').html('');
                    $('#append_error').html('No location data found.');
                    return;
                }

                var address = response[0].address;
                var city = response[0].city;
                var country = response[0].country;
                var addressParts = [];
                if (address) addressParts.push(address);
                if (city) addressParts.push(city);
                if (country) addressParts.push(country);
                var fullAddress = addressParts.join(', ');
                var latitude = response[0].latitude;
                var longitude = response[0].longitude;

                $('#append_address').html('Address: ' + fullAddress);
                $('#append_error').html('');

                // Update Google Maps iframe
                var mapSrc = `https://maps.google.com/maps?q=${latitude},${longitude}&z=14&output=embed`;
                var mapHref = `https://maps.google.com/maps?q=${latitude},${longitude}&z=14`;

                $('#mapFrame').attr('src', mapSrc).show(); // Set src and show iframe
                $('#mapLink').attr('href', mapHref).show();

                $('#trailer_no').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#trailer_no').prop('disabled', false);
                console.log('Error:', error);
                $('#append_address').html('');
                $('#append_error').html('Request failed');
            }
        });
    });

</script>
@endsection