@extends('layout.app')
@section('title', 'Trailers')
@section('sub-title', 'Trailers')
@section('content')
<div class="mb-3 mt-3 trailer_number">
    <label class="form-label">Select Trailer Number</label>
    <select id="trailer_no">
        @foreach($trailers as $row)
        <option value="{{ $row->trailer_num }}">{{ $row->trailer_num }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3 mt-3" id="address">
    <label class="form-label">Address</label>
    <div id="append_address"></div>
</div>

<div>
    
</div>
<div id="map"></div>

@endsection

@section('js_scripts')
<script>
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

// Initialize the map with the current location when the page loads
setCurrentLocation();

$('#trailer_no').on('change', function() {
    var trailerId = $(this).val(); // Get the selected trailer ID
    console.log('Selected Trailer ID:', trailerId);

    // If map is not already initialized, initialize it
    if (!map) {
        map = L.map('map').setView([0, 0], 2); // Default view
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([0, 0]).addTo(map); // Initialize the marker
    }

    var apiUrl = `https://gemco.forzatrans.app/api/Trailers/lastposition/${trailerId}`;
    var apiKey = 'pT7#f9@Lk2^bWz8!xQeV3$Mn6*ArYt1&JdF4+Gh9%UzXo7=KpL';

    $.ajax({
        url: apiUrl,
        type: 'GET',
        headers: {
            'X-API-Key': apiKey,
            'Content-Type': 'application/json'
        },
        timeout: 30000,
        success: function(response) {
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

            $('#append_address').html(fullAddress);

            // Update map position
            map.setView([latitude, longitude], 10);
            marker.setLatLng([latitude, longitude])
                .bindPopup('Trailer Location: ' + latitude + ', ' + longitude)
                .openPopup();
        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
            alert('Request failed: ' + error);
        }
    });
});
</script>



@endsection