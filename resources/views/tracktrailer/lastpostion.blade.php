@extends('layout.app')
@section('title', 'Trailers')
@section('sub-title', __('messages.Trailer'))

@section('content')
<div class="main_cont_outer">
    <div class="card card-container">
        <div class="card-body">
                        <h4 class="mb-4">{{ __('messages.get_supplier_truck_position') }}</h4>

            <!-- <div class="mb-3 mt-3 trailer_number">
                <label class="form-label"> {{ __('messages.Select Trailer Number') }} </label>
                <select id="trailer_no" class="searchable-select">
                    <option value=""> {{ __('messages.Select a Trailer') }} </option>
                    @foreach($trailers as $row)
                        <option value="{{ $row->trailer_num }}">{{ $row->trailer_num }}</option>
                    @endforeach
                </select>
            </div> -->
<div class="mb-3">
    <label for="supplier_id" class="form-label">{{ __('messages.supplier') }}</label>
    <select name="supplier_id" id="supplier_id" class="form-select searchable-select-supplier">
        <option value="">{{ __('messages.select_supplier') }}</option>
        @foreach($assignedSuppliers as $assigned)
            @if ($assigned->supplier)
                <option value="{{ $assigned->supplier->id }}">
                    {{ $assigned->supplier->dba ?? $assigned->supplier->company_name }}
                </option>
            @endif
        @endforeach
    </select>
    @error('supplier_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="mb-3">
    <label for="truck_number" class="form-label">{{ __('messages.truck_number') }}</label>
    <select name="truck_number" id="truck_number" class="form-select searchable-select">
        <option value="">{{ __('messages.select_truck') }}</option>
    </select>
    @error('truck_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

            <div class="mb-3 mt-3 address-container" id="address">
                <div id="append_address" class="address-box"></div>
                <div id="append_error" class="text-danger error-message"></div>
            </div>
            <!-- <div id="map"></div> -->
            <iframe id="mapFrame" width="800" height="500" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="" style="display: none;"></iframe>
            <br />
            <small>
                <a id="mapLink" href="#" style="color:#0000FF;text-align:left; display: none;" target="_blank"> {{ __('messages.See map bigger') }} </a>
            </small>
        </div>
    </div>
</div>


@endsection

@section('js_scripts')
<script>
    $(document).ready(function() {
            if ($.fn.select2) { 
        $('.searchable-select').select2({
            placeholder: "{{ __('messages.Select a Trailer') }}",
            allowClear: true // Adds a clear button
        });

         $('.searchable-select-supplier').select2({
            placeholder: "{{ __('messages.select_supplier') }}",
            allowClear: true // Adds a clear button
        });
    } else {
                console.error("Select2 is not loaded!");
            }

            $('#supplier_id').on('change', function () {
            let supplierId = $(this).val();

            if (supplierId) {
                $.ajax({
                    url: `/supplier/${supplierId}/trucks`,
                    type: 'GET',
                    success: function (response) {
                        $('#truck_number').empty();
                        $('#truck_number').append(`<option value="">@lang('messages.select_truck')</option>`);

                        response.forEach(function (truck) {
                            $('#truck_number').append(`<option value="${truck.truck_number}">${truck.truck_number}</option>`);
                        });
                    }
                });
            } else {
                $('#truck_number').html('<option value="">@lang("messages.select_truck")</option>');
            }
        });
    });
    var map;
    var marker;

    // Function to get current location and set map view
    function setCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
            

                // If map is not already initialized, initialize it
                if (!map) {
                    map = L.map('map').setView([latitude, longitude], 13); // Set view to current location
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    marker = L.marker([latitude, longitude]).addTo(map); // Place marker at current location
                    marker.bindPopup('Current Location: ' + latitude + ', ' + longitude).openPopup();
                } else {
                    // Update map and marker to current location
                    map.setView([latitude, longitude], 13);
                    marker.setLatLng([latitude, longitude])
                        .bindPopup('Current Location: ' + latitude + ', ' + longitude)
                        .openPopup();
                }
            }, function(error) {
                console.log('Error getting current location:', error);
                alert('Error getting current location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    // setCurrentLocation();


    $('#truck_number').on('change', function() {
        $('#append_address').html('');
        $('#append_error').html('');
        var trailerId = $(this).val(); // Get the selected trailer ID
        console.log('Selected Trailer ID:', trailerId);

        // If map is not already initialized, initialize it
        // if (!map) {
        //     map = L.map('map').setView([0, 0], 2); // Default view
        //     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //         attribution: '&copy; OpenStreetMap contributors'
        //     }).addTo(map);

        //     marker = L.marker([0, 0]).addTo(map); 
        // }

        var apiUrl = `https://gemco.forzatrans.app/api/Trailers/lastposition/${trailerId}`;
        var apiKey = 'pT7#f9@Lk2^bWz8!xQeV3$Mn6*ArYt1&JdF4+Gh9%UzXo7=KpL';

        $.ajax({
            url: apiUrl,
            type: 'GET',
            beforeSend: function() {
            $('#truck_number').prop('disabled', true);
            $('#mapFrame, #mapLink').hide();
            },

            headers: {
                'X-API-Key': apiKey,
                'Content-Type': 'application/json'
            },
            timeout: 30000,
            success: function(response) {
                if (!response.length) {
                    $('#truck_number').prop('disabled', false);
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

                $('#append_address').html('Address: '+fullAddress);
                $('#append_error').html('');

                // Update Google Maps iframe
                var mapSrc = `https://maps.google.com/maps?q=${latitude},${longitude}&z=14&output=embed`;
                var mapHref = `https://maps.google.com/maps?q=${latitude},${longitude}&z=14`;

                $('#mapFrame').attr('src', mapSrc).show(); // Set src and show iframe
                $('#mapLink').attr('href', mapHref).show();
                // Update map position
                // map.setView([latitude, longitude], 10);
                // marker.setLatLng([latitude, longitude])
                //     .bindPopup('Trailer Location: ' + latitude + ', ' + longitude)
                //     .openPopup();
                $('#truck_number').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#truck_number').prop('disabled', false);
                console.log('Error:', error);
                $('#append_address').html('');
                $('#append_error').html('Request failed');
            }
        });
    });

</script>

@endsection