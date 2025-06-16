@section('title', 'Supplier Equipment')
{{-- @section('sub-title', 'Supplier Equipment') --}}
@section('sub-title', __('messages.Supplier Equipment'). ' | Company: ' . $supplier->company_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_units.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}
        </a>
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

    <!-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul>    
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif -->

    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('supplier_units.store', $supplier->id) }}" method="POST">
                @csrf
                <div class="form-group mb-3 mt-3">
                    <label for="unit_type" class="form-label"> {{ __('messages.Unit Type') }} <span class="text-danger">*</span></label>
                    <select name="unit_type" class="form-control">
                        <option value="">{{ __('messages.Select Unit Type') }} </option>
                        <option value="53' Trailer" {{ old('unit_type') == "53' Trailer" ? 'selected' : '' }}>{{ __('messages.53\' Trailer') }}</option>
                        <option value="48' Trailer" {{ old('unit_type') == "48' Trailer" ? 'selected' : '' }}>{{ __('messages.48\' Trailer') }}</option>
                        <option value="53' Flatbed" {{ old('unit_type') == "53' Flatbed" ? 'selected' : '' }}>{{ __('messages.53\' Flatbed') }}</option>
                        <option value="48' Flatbed" {{ old('unit_type') == "48' Flatbed" ? 'selected' : '' }}>{{ __('messages.48\' Flatbed') }}</option>
                        <option value="Semi Lowboy (StepDeck)" {{ old('unit_type') == "Semi Lowboy (StepDeck)" ? 'selected' : '' }}>{{ __('messages.Semi Lowboy (StepDeck)') }}</option>
                        <option value="Lowboy (Double Drop)" {{ old('unit_type') == "Lowboy (Double Drop)" ? 'selected' : '' }}>{{ __('messages.Lowboy (Double Drop)') }}</option>
                        <option value="3.5 Tn. Truck" {{ old('unit_type') == "3.5 Tn. Truck" ? 'selected' : '' }}>{{ __('messages.3.5 Tn. Truck') }}</option>
                        <option value="10 Tn. Truck" {{ old('unit_type') == "10 Tn. Truck" ? 'selected' : '' }}>{{ __('messages.10 Tn. Truck') }}</option>
                        <option value="Pickup Truck" {{ old('unit_type') == "Pickup Truck" ? 'selected' : '' }}>{{ __('messages.Pickup Truck') }}</option>
                        <option value="Reefer" {{ old('unit_type') == "Reefer" ? 'selected' : '' }}>{{ __('messages.Reefer') }}</option>
                    </select>
                    @error('unit_type')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror  
        </div>
        <!-- <div class="form-group mb-3">
            <label for="unit_number" class="form-label"> {{ __('messages.Unit Number') }} <span class="text-danger">*</span></label>
       
            <input type="text" name="unit_number" class="form-control" value="{{ old('unit_number') }}">

            @error('unit_number')
                {{ $message }}
            @enderror  

        </div> -->

            <div class="form-group mb-3">
            <label for="truck_number" class="form-label">{{__('messages.Truck Number')}} <span class="text-danger">*</span> <small class="text-muted d-block">
    {{ __('messages.Truck Number Note') }}
</small> </label>
            <input 
                type="text" 
                class="form-control" 
                id="truck_number" 
                name="truck_number" 
                value="{{ old('truck_number') }}" 
            >
            @error('truck_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="license_plate" class="form-label"> {{ __('messages.License Plate') }} <span class="text-danger">*</span></label>
            <!-- <select name="license_plate" class="form-control">
                <option value="">{{ __('messages.Select License Plate') }}</option>
                <option value="ABC-1234" {{ old('license_plate') == 'ABC-1234' ? 'selected' : '' }}>ABC-1234</option>
                <option value="XYZ-5678" {{ old('license_plate') == 'XYZ-5678' ? 'selected' : '' }}>XYZ-5678</option>
                <option value="LMN-9101" {{ old('license_plate') == 'LMN-9101' ? 'selected' : '' }}>LMN-9101</option>
                <option value="PQR-2468" {{ old('license_plate') == 'PQR-2468' ? 'selected' : '' }}>PQR-2468</option>
                <option value="JKL-1357" {{ old('license_plate') == 'JKL-1357' ? 'selected' : '' }}>JKL-1357</option>
            </select> -->
            <input type="text" name="license_plate" class="form-control" value="{{ old('license_plate') }}">

            @error('license_plate')
                {{ $message }}
            @enderror  
        </div>

                <div class="form-group mb-3">
                    <label for="state" class="form-label"> {{ __('messages.State') }} <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" >
                    @error('state')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror  
                </div>
        
                <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Create Unit') }} </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
    $(document).ready(function() {

    });
</script>

@endsection