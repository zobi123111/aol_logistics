@section('title', 'Supplier Service')
{{-- @section('sub-title', 'Supplier Service') --}}
@section('sub-title', __('messages.Supplier Service'). ' | Company: ' . $supplier->company_name)
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('services.index',  $supplierId) }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
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
            <form action="{{ route('services.update', ['supplierId' => $supplierId, 'serviceId' => $service->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3 mt-3">
                    <label for="service_type" class="form-label">{{ __('messages.Service Type') }} <span class="text-danger">*</span></label>
                    <select name="service_type" id="service_type" class="form-control">
                        <option value="">{{ __('messages.Select Service Type') }}</option>
                        <option value="freight" {{ (old('service_type', $service->service_type ?? '') == 'freight') ? 'selected' : '' }}>{{ __('messages.Freight') }}</option>
                        <option value="warehouse" {{ (old('service_type', $service->service_type ?? '') == 'warehouse') ? 'selected' : '' }}>{{ __('messages.Warehouse') }}Warehouse</option>
                    </select>
                    @error('service_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="service_name" class="form-label"> {{ __('messages.Service Name') }}</label>
                    <input type="text" name="service_name" id="service_name" class="form-control"  value="{{ isset($service) ? $service->service_name : old('service_name') }}">
                    @error('service_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div id="freight_fields" style="display: {{ isset($service) && $service->service_type == 'freight' ? 'block' : 'none' }};">
                <div class="form-group mb-3">
                    <label for="origin" class="form-label"> {{ __('messages.Origin') }} <span class="text-danger">*</span></label>
                    <select name="origin" id="origin" class="form-control">
                    <option value="">{{ __('messages.Select Origin') }}</option>
                        @foreach($origins as $origin)
                            <option value="{{ $origin->id }}" 
                                {{ (isset($service) && $service->origin == $origin->id) || old('origin') == $origin->id ? 'selected' : '' }}>
                                {{ $origin->name ? $origin->name : ($origin->street . ', ' . $origin->city . ', ' . $origin->state . ', ' . $origin->zip . ', ' . $origin->country) }}
                                </option>
                        @endforeach
                    </select>
                    @error('origin')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="destination" class="form-label"> {{ __('messages.Destination') }} <span class="text-danger">*</span></label>
                    <select name="destination" id="destination" class="form-control">
                    <option value="">{{ __('messages.Select Destination') }}</option>
                    @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" 
                                {{ (isset($service) && $service->destination == $destination->id) || old('destination') == $destination->id ? 'selected' : '' }}>
                                {{$destination->name ? $destination->name :  $destination->street.', '. $destination->city.', '. $destination->state.', '. $destination->zip.', '. $destination->country }}

                            </option>
                        @endforeach
                    </select>
                    @error('destination')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="warehouse_fields" style="display: {{ isset($service) && $service->service_type == 'warehouse' ? 'block' : 'none' }};">
                <div class="form-group mb-3">
                    <label for="street" class="form-label">{{ __('messages.Street Address') }} <span class="text-danger">*</span></label>
                    <input type="text" name="street" id="street" class="form-control" value="{{ isset($service) ? $service->street : old('street') }}">
                    @error('street')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="city" class="form-label">{{ __('messages.City') }} <span class="text-danger">*</span></label>
                    <input type="text" name="city" id="city" class="form-control" value="{{ isset($service) ? $service->city : old('city') }}">
                    @error('city')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="state" class="form-label">{{ __('messages.State') }} <span class="text-danger">*</span></label>
                    <input type="text" name="state" id="state" class="form-control" value="{{ isset($service) ? $service->state : old('state') }}">
                    @error('state')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="zip" class="form-label">{{ __('messages.Zip Code') }} <span class="text-danger">*</span></label>
                    <input type="text" name="zip" id="zip" class="form-control" value="{{ isset($service) ? $service->zip : old('zip') }}">
                    @error('zip')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="country" class="form-label">{{ __('messages.Country') }} <span class="text-danger">*</span></label>
                    <select name="country" class="form-control @error('country') is-invalid @enderror">
                        <option value=""> {{ __('messages.Select Country') }} </option>
                        @foreach(['USA', 'Mexico', 'UK', 'Germany', 'France', 'Australia', 'India', 'China', 'Japan', 'Brazil'] as $country)
                            <option value="{{ $country }}" 
                                {{ (isset($service) && $service->country == $country) || old('country') == $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

                <!-- Cost -->
                <div class="form-group mb-3">
                    <label for="cost" class="form-label"> {{ __('messages.Cost') }} (USD)<span class="text-danger">*</span></label>
                    <input type="text" step="0.01" name="cost" class="form-control" value="{{ old('cost', $service->cost) }}">
                    @error('cost')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror  
                </div>

                <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Update Service') }} </button>
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

</script>

@endsection