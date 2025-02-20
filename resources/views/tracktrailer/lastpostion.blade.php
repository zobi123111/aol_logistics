@extends('layout.app')
@section('title', 'Trailers')
@section('sub-title', 'Trailers')
@section('content')
<div class="main_cont_outer">
    <div class="card card-container">
        <div class="card-body">
            <div class="mb-3 mt-3 trailer_number">
                <label class="form-label">Select Trailer Number</label>
                <select id="trailer_no" class="searchable-select">
                    <option value="">Select a Trailer</option>
                    @foreach($trailers as $row)
                        <option value="{{ $row->trailer_num }}">{{ $row->trailer_num }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 mt-3 address-container" id="address">
                <div id="append_address" class="address-box"></div>
                <div id="append_error" class="text-danger error-message"></div>
            </div>
            <div id="map"></div>
        </div>
    </div>
</div>


@endsection

@section('js_scripts')
<script>
    $(document).ready(function() {
    $('.searchable-select').select2({
        placeholder: "Select a Trailer",
        allowClear: true // Adds a clear button
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

setCurrentLocation();


$('#trailer_no').on('change', function() {
    $('#append_address').html('');
    $('#append_error').html('');
    var trailerId = $(this).val(); // Get the selected trailer ID
    console.log('Selected Trailer ID:', trailerId);

    // If map is not already initialized, initialize it
    if (!map) {
        map = L.map('map').setView([0, 0], 2); // Default view
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([0, 0]).addTo(map); 
    }

    var apiUrl = `https://gemco.forzatrans.app/api/Trailers/lastposition/${trailerId}`;
    var apiKey = 'pT7#f9@Lk2^bWz8!xQeV3$Mn6*ArYt1&JdF4+Gh9%UzXo7=KpL';

    $.ajax({
        url: apiUrl,
        type: 'GET',
        beforeSend: function() {
        $('#trailer_no').prop('disabled', true);
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

            $('#append_address').html('Address: '+fullAddress);
            $('#append_error').html('');

            // Update map position
            map.setView([latitude, longitude], 10);
            marker.setLatLng([latitude, longitude])
                .bindPopup('Trailer Location: ' + latitude + ', ' + longitude)
                .openPopup();
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