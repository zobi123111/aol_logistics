@extends('layout.app')

@section('title', 'Load Details')
@section('sub-title', __('messages.Loads'))
@section('content')
<div class="main_cont_outer">
<div class="create_btn mb-3">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i> {{ __('messages.Back') }} 
        </a>
    </div>
    <div class="container">
    <h2 class="mb-4">{{ __('messages.Load Details') }} </h2>

    <!-- Load Information Card -->
    <div class="card mb-4">
        <div class="card-header blue_icon_color">
            Load Information
        </div>
        <div class="card-body">
                @php
                    $payerOptions = [
                        'client' => __('messages.Client directly'),
                        'another_party' => __('messages.Another party will pay for the load')
                    ];
                @endphp
            <p><strong> {{ __('messages.AOL Number') }} :</strong> {{ $load->aol_number }}</p>
            <p><strong> {{ __('messages.Origin') }} :</strong> 
            {{  $load->origindata
                    ? ($load->origindata->name ?: ($load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country))
                    : 'N/A'; }}
                    
                </p>
            <p><strong> {{ __('messages.Destination') }} :</strong> {{$load->destinationdata
                    ? ($load->destinationdata->name ?: ($load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country))
                    : 'N/A' }}</p>
            <p><strong> {{ __('messages.Service Type') }} :</strong> {{ $load->service_type }}</p>
            @if(!isClientUser())
            <p><strong> {{ __('messages.Payer') }} :</strong> {{ $payerOptions[$load->payer] ?? 'N/A' }}</p>
            @endif
            <p><strong> {{ __('messages.Equipment Type') }} :</strong> {{ $load->equipment_type }}</p>
            @if(!isClientUser())
            <p><strong> {{ __('messages.Supplier') }} :</strong> {{ $load->supplier ? $load->supplier->company_name : 'N/A' }}</p>
            @endif
            <p><strong> {{ __('messages.Trailer Number') }} :</strong> {{ $load->truck_number ?? 'N/A' }}</p>
            <p><strong> {{ __('messages.Port of Entry') }} :</strong> {{ $load->port_of_entry ?? 'N/A' }}</p>
            <p><strong> {{ __('messages.Weight') }} :</strong> {{ $load->weight !== null ? number_format($load->weight, 2, '.', ',') . ' lbs' : 'N/A' }}</p>
            <p><strong> {{ __('messages.Delivery Deadline') }} :</strong> {{ $load->delivery_deadline->format('M. j, Y') }}</p>
            <p><strong> {{ __('messages.Customer PO') }} :</strong> {{ $load->customer_po ?? 'N/A' }}</p>
            <p><strong> {{ __('messages.Hazmat') }} :</strong> {!! $load->is_hazmat ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>
            <p><strong> {{ __('messages.Inbond') }} :</strong> {!! $load->is_inbond ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>
            <p><strong> {{ __('messages.Inspection') }} :</strong> {!! $load->inspection ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</p>

            <p><strong> {{ __('messages.Status') }} :</strong> 
                <span class="badge 
                    {{ $load->status == 'Pending' ? 'bg-warning' : ($load->status == 'Completed' ? 'bg-success' : 'bg-secondary') }}">
                    {{ $load->status }}
                </span>
            </p>
            <p><strong>{{ __('messages.Created By') }} :</strong> {{ optional($load->creator)->fname . ' ' . optional($load->creator)->lname ?? 'NA'}}</p>

            <p><strong>{{ __('messages.Created At') }} :</strong> {{ $load->created_at->format('M. j, Y H:i') }}</p>
        </div>
    </div>

    <!-- Assigned Services Table -->
    <h3>{{ __('messages.Assigned Services') }} </h3>
    <table class="table table-bordered" id="assigned">
        <thead class="bg-secondary text-white">
            <tr>
                @if(!isClientUser())
                <th>{{ __('messages.Supplier') }} </th>
                @endif
                <th> {{ __('messages.Service Name') }}  </th>
                <th>{{ __('messages.Service Details') }} </th>
                @if(!isClientUser())
                <th>{{ __('messages.Cost') }} </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($load->assignedServices as $assignedService)
                <tr>
                    @if(!isClientUser())
                    <td>{{ $assignedService->supplier->dba ?? $assignedService->supplier->company_name }}</td>
                    @endif
                    <td>{{ $assignedService->service->service_name ?? 'NA' }}</td>
                    <td>
                    {{ $assignedService->service->origindata 
                        ? ($assignedService->service->origindata->name 
                            ?: ($assignedService->service->origindata->street . ', ' . $assignedService->service->origindata->city . ', ' . $assignedService->service->origindata->state . ', ' . $assignedService->service->origindata->zip . ', ' . $assignedService->service->origindata->country)) 
                        : 'N/A' }}  
                    →  
                    {{ $assignedService->service->destinationdata 
                        ? ($assignedService->service->destinationdata->name 
                            ?: ($assignedService->service->destinationdata->street . ', ' . $assignedService->service->destinationdata->city . ', ' . $assignedService->service->destinationdata->state . ', ' . $assignedService->service->destinationdata->zip . ', ' . $assignedService->service->destinationdata->country)) 
                        : 'N/A' }}


                    </td>
                    @if(!isClientUser())
                    <td>${{ number_format($assignedService->service->cost, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">{{ __('messages.No assigned services') }} </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="container">
<div class="card mb-4">
        <div class="card-body text">
            <div class="card-header blue_icon_color">
                {{ __('messages.Current Trailer Position') }} 
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
                {{ __('messages.See map bigger') }} 
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

        var trailerId = `{{ $load->truck_number }}`;
        console.log('Selected Trailer ID:', trailerId);

        if (!trailerId) {
            // $('#append_error').html('No trailer number found.');
            $('#append_error').html(@json(__('messages.No trailer number found')));
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
                    // $('#append_error').html('No location data found.');
                    $('#append_error').html(@json(__('messages.No location data found')));

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