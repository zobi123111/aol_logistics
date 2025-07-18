@section('title', 'client_service')
@section('sub-title', __('messages.client_service'). ' | Company: ' . $client->company_name)
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('client_services.index',  encode_id($client->id)) }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i>  {{ __('messages.Back') }} </a>
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

    <div class="card card-container">
        <div class="card-body">
        <form action="{{ route('client_services.update', ['userId' => $client->id, 'serviceId' => $clientService->id]) }}" method="POST">
        @csrf
        @method('PUT')

    <!-- Service Type Selection -->
    <div class="form-group mb-3">
        <label for="service-type" class="form-label">{{ __('messages.service_type') }} <span class="text-danger">*</span></label>
       <select id="service-type" class="form-control" name="service_type" onchange="updateMasterServices()">
            <option value="">{{ __('messages.Select Service Type') }}</option>
            @foreach ($serviceTypes as $type)
                @php
                    // Convert snake_case to Title Case for translation key
                    $labelKey = ucwords(str_replace('_', ' ', $type));
                    $selected = old('service_type', $clientService->masterService->service_type ?? '') == $type ? 'selected' : '';
                @endphp
                <option value="{{ $type }}" {{ $selected }}>
                    {{ __('messages.' . $labelKey) }}
                </option>
            @endforeach
        </select>
        @error('service_type')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Master Service Selection -->
    <div class="form-group mb-3">
        <label for="master-service" class="form-label">{{ __('messages.master_service') }} <span class="text-danger">*</span></label>
        <select name="master_service_id" id="master-service" class="form-control">
            <option value="">Select Master Service</option>
            @foreach ($masterServices as $type => $services)
                @foreach ($services as $service)
                    @php
                        $locationDetails = '';
                        if ($type === 'freight') {
                            $origin = $service->origindata ? $service->origindata->name : 'Unknown Origin';
                            $destination = $service->destinationdata ? $service->destinationdata->name : 'Unknown Destination';
                            $locationDetails = " (Origin: $origin, Destination: $destination)";
                        } elseif ($type === 'warehouse') {
                            $locationDetails = " ({$service->street}, {$service->city}, {$service->state}, {$service->zip}, {$service->country})";
                        }
                    @endphp
                    <option value="{{ $service->id }}" data-type="{{ $type }}" 
                        {{ old('master_service_id', $clientService->master_service_id) == $service->id ? 'selected' : '' }} 
                        data-location="{{ $locationDetails }}">
                        {{ $service->service_name }}{{ $locationDetails }}
                    </option>
                @endforeach
            @endforeach
        </select>
        @error('master_service_id')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Cost Input -->
    <div class="form-group mb-3">
        <label for="cost" class="form-label">{{ __('messages.cost') }}  <span class="text-danger">*</span></label>
        <input type="text" name="cost" class="form-control" value="{{ old('cost', $clientService->cost) }}">
        @error('cost')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
    </div>
        <fieldset class="form-group mb-3 border p-3 rounded">
                    <legend class="text-center"> {{ __('messages.set_future_cost') }}</legend>
                     <div class="form-group mb-3">
    <label for="schedule-cost" class="form-label">{{ __('messages.future_cost') }} </label>
    <input type="text" name="schedule_cost" id="schedule-cost" class="form-control" value="{{ old('schedule_cost', $clientService->schedule_cost) }}">
    @error('schedule_cost')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>
    <!-- Service Date (optional) -->
    <div class="form-group mb-3">
        <label for="service-date" class="form-label">{{ __('messages.effective_date') }} </label>
        <input type="date" name="service_date" class="form-control" value="{{ old('service_date', $clientService->service_date ? $clientService->service_date->format('Y-m-d') : '') }}">
        @error('service_date')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
    </div>
   
        </fieldset>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Add Service') }} </button>
</form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
    $(document).ready(function() {
        // $("#freight_fields, #warehouse_fields").hide();

// Listen for changes in selection (assuming a radio button or select dropdown exists)
// $("input[name='service_type']").change(function () {
//     let selectedType = $(this).val();
// console.log(selectedType)
//     if (selectedType === "freight") {
//         $("#freight_fields").show();
//         $("#warehouse_fields").hide();
//     } else if (selectedType === "warehouse") {
//         $("#warehouse_fields").show();
//         $("#freight_fields").hide();
//     } else {
//         $("#freight_fields, #warehouse_fields").hide();
//     }
// });

// // Trigger change event to maintain the state if the page reloads with a selected option
// $("input[name='shipping_type']:checked").trigger("change");

function toggleFields() {
            let serviceType = $('#service_type').val();
            $('#freight_fields').toggle(serviceType === 'freight');
            $('#warehouse_fields').toggle(serviceType === 'warehouse');
        }
        toggleFields(); // Run on page load to retain values

        $('#service_type').change(function() {
            toggleFields();
        });
    });
    function updateMasterServices() {
        var serviceType = document.getElementById('service-type').value;
        var masterServiceSelect = document.getElementById('master-service');
        var options = masterServiceSelect.getElementsByTagName('option');
        // masterServiceSelect.value = ""; 
        // Enable the master service select box once a service type is selected
        masterServiceSelect.disabled = false;

        // Loop through the options and only display the relevant ones based on the selected service type
        for (var i = 0; i < options.length; i++) {
            var option = options[i];
            if (option.dataset.type !== serviceType && option.value !== "") {
                option.style.display = 'none'; // Hide options not matching the selected service type
            } else {
                option.style.display = 'block'; // Show relevant options
            }
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
    updateMasterServices();
});
</script>

@endsection