@section('title', 'Supplier Service')
{{-- @section('sub-title', 'Supplier Service') --}}
@section('sub-title', __('messages.Service'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a  class="btn btn-primary create-button btn_primary_color"
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
        <form action="{{ route('master-services.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3 mt-3">
                    <label for="service_type" class="form-label">{{ __('messages.Service Type') }} <span class="text-danger">*</span></label>
            <select name="service_type" id="service_type" class="form-control" >
                <option value="">{{ __('messages.Select Service Type') }}</option>
                <option value="freight" {{ old('service_type') == 'freight' ? 'selected' : '' }}>{{ __('messages.Freight') }}</option>
                <option value="warehouse" {{ old('service_type') == 'warehouse' ? 'selected' : '' }}>{{ __('messages.Warehouse') }}</option>
            </select>
            @error('service_type')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            </div>
            <div class="form-group mb-3">
                <label for="service_name" class="form-label"> {{ __('messages.Service Name') }}</label>
                <input type="text" name="service_name" id="service_name" class="form-control" value="{{ old('service_name') }}">
                @error('service_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div id="freight_fields" style="display: none;">
            <div class="form-group mb-3">
                <label for="origin" class="form-label"> {{ __('messages.Origin') }} <span class="text-danger">*</span></label>
                <select name="origin" id="origin" class="form-control">
                    <option value="">{{ __('messages.Select Origin') }}</option>
                    @foreach($origins as $origin)
                        <option value="{{ $origin->id }}" {{ old('origin') == $origin->id ? 'selected' : '' }}>
                        {{ $origin->name ? $origin->name : ($origin->street . ', ' . $origin->city . ', ' . $origin->state . ', ' . $origin->zip . ', ' . $origin->country) }}
                        </option>
                    @endforeach
                </select>
                @error('origin')
                    <div class="text-danger">
                         {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="destination" class="form-label"> {{ __('messages.Destination') }} <span class="text-danger">*</span></label>
                <select name="destination" id="destination" class="form-control">
                    <option value="">{{ __('messages.Select Destination') }}</option>
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" {{ old('destination') == $destination->id ? 'selected' : '' }}>
                        {{$destination->name ? $destination->name :  $destination->street.', '. $destination->city.', '. $destination->state.', '. $destination->zip.', '. $destination->country }}
                        </option>
                    @endforeach
                </select>
                @error('destination')
                    <div class="text-danger">
                         {{ $message }}
                    </div>
                @enderror
            </div>
            </div>
            <div id="warehouse_fields" style="display: none;">
    <div class="form-group mb-3">
        <label for="street" class="form-label">{{ __('messages.Street Address') }} <span class="text-danger">*</span></label>
        <input type="text" name="street" id="street" class="form-control" value="{{ old('street') }}">
        @error('street')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="city" class="form-label">{{ __('messages.City') }} <span class="text-danger">*</span></label>
        <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
        @error('city')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="state" class="form-label">{{ __('messages.State') }} <span class="text-danger">*</span></label>
        <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}">
        @error('state')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="zip" class="form-label">{{ __('messages.Zip Code') }} <span class="text-danger">*</span></label>
        <input type="text" name="zip" id="zip" class="form-control" value="{{ old('zip') }}">
        @error('zip')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
            <label for="country" class="form-label"> {{ __('messages.Country') }} <span class="text-danger">*</span></label>
            <select name="country" class="form-control @error('country') is-invalid @enderror" >
                <option value=""> {{ __('messages.Select Country') }} </option>
                <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>United States</option>
                <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>Germany</option>
                <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France</option>
                <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>China</option>
                <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>Japan</option>
                <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>Brazil</option>
            </select>
            @error('country')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
</div>

            <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Create Service') }} </button>

        </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
    $(document).ready(function() {
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