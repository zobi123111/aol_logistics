@section('title', 'Supplier Service')
{{-- @section('sub-title', 'Supplier Service') --}}
@section('sub-title', __('messages.Supplier Service'). ' | Company: ' . $supplier->company_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_services.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i>  {{ __('messages.Back') }} </a>

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
        <form action="{{ route('supplier_services.store', $supplier->id) }}" method="POST">
        @csrf

        <div class="form-group mb-3">
    <label for="service-type" class="form-label">Service Type <span class="text-danger">*</span></label>
   <select id="service-type" class="form-control" name="service_type">
    <option value="">{{ __('messages.Select Service Type') }}</option>
    @foreach ($serviceTypes as $type)
        @php
            // Convert snake_case to Title Case for translation key
            $translationKey = str_replace('_', ' ', $type);
            $translatedLabel = __('messages.' . ucwords($translationKey));
        @endphp
        <option value="{{ $type }}" {{ old('service_type') == $type ? 'selected' : '' }}>
            {{ $translatedLabel }}
        </option>
    @endforeach
</select>

    @error('service_type')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="master-service" class="form-label">Master Service <span class="text-danger">*</span></label>
    <select name="master_service_id" id="master-service" class="form-control" disabled>
        <option value="">Select Master Service</option>
        @foreach ($masterServices as $type => $services)
    @foreach ($services as $service)
        @php
            // Prepare location details based on service type
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
            {{ old('master_service_id') == $service->id ? 'selected' : '' }}>
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

<div class="form-group mb-3">
    <label for="cost" class="form-label">Cost <span class="text-danger">*</span></label>
    <input type="text" name="cost" class="form-control" value="{{ old('cost') }}">
    @error('cost')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>
<fieldset class="form-group mb-3 border p-3 rounded">
    <legend class="text-center"> {{ __('messages.set_future_cost') }}</legend>

<div class="form-group mb-3">
    <label for="schedule-cost" class="form-label">{{ __('messages.future_cost') }}</label>
    <input type="text" name="schedule_cost" id="schedule-cost" class="form-control" value="{{ old('schedule_cost') }}">
    @error('schedule_cost')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="service-date" class="form-label">{{ __('messages.effective_date') }} </label>
    <input type="date" name="service_date" class="form-control" value="{{ old('service_date') }}">
    @error('service_date')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>
</fieldset>
<button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Add Service') }} </button>
    </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
    $(document).ready(function () {
        const $serviceTypeSelect = $('#service-type');
        const $masterServiceSelect = $('#master-service');
        
        // Initialize Select2
        $masterServiceSelect.select2({
            placeholder: 'Select Master Service',
            allowClear: true,
            width: '100%'
        });

        // Store all the master service options initially
        const allOptions = $masterServiceSelect.html();

        $serviceTypeSelect.on('change', function () {
            const selectedType = $(this).val();

            // Reset and enable the Master Service select
            $masterServiceSelect.prop('disabled', !selectedType).html('<option value="">Select Master Service</option>');

            if (selectedType) {
                // Filter and show only matching services
                const filteredOptions = $(allOptions).filter(`[data-type="${selectedType}"]`);
                $masterServiceSelect.append(filteredOptions).prop('disabled', false).trigger('change');
            } else {
                $masterServiceSelect.trigger('change');
            }
        });
          // Trigger change on page load to select the correct master service if editing
    if ($serviceTypeSelect.val()) {
        $serviceTypeSelect.trigger('change');
    }
    });
</script>

@endsection