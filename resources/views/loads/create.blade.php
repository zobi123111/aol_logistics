@section('title', 'Load')
{{-- @section('sub-title', 'Load') --}}
@section('sub-title', __('messages.Loads'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
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
        <form action="{{ route('loads.store') }}" method="POST">
        @csrf

        <div class="form-group form-group mb-3 mt-3">
            <label for="service_type" class="form-label">{{ __('messages.service_type') }} <span class="text-danger">*</span></label>
            <select name="service_type" id="service_type" class="form-control">
                <!-- <option value="">{{ __('messages.Select Service Type') }} </option> -->
                <option value="Land Freight" {{ old('service_type') == 'Land Freight' ? 'selected' : '' }}>{{ __('messages.Land Freight') }}</option>
                        <option value="Air Freight" {{ old('service_type') == 'Air Freight' ? 'selected' : '' }}>{{ __('messages.Air Freight') }}</option>
                        <option value="Ocean Freight" {{ old('service_type') == 'Ocean Freight' ? 'selected' : '' }}>
                        {{ __('messages.Ocean Freight') }}</option>
            </select>
            @error('service_type')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>
        @if(!isSupplierUser())
        <!-- <div class="form-group mb-3">
            <label for="supplier_id" class="form-label">{{ __('messages.Select Supplier') }} </label>
            <select name="supplier_id" id="supplier_id" class="form-control select2">
                <option value="">{{ __('messages.Select Supplier') }} </option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->company_name }}
                    </option>
                @endforeach
            </select>
            @error('supplier_id')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div> -->
        @endif
        
        <div class="form-group mb-3">
            <label for="origin" class="form-label">{{ __('messages.Origin') }} <span class="text-danger">*</span></label>
            <select name="origin" id="origin" class="form-control select2">
                <option value="">{{ __('messages.Select Origin') }} </option>
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
            <label for="destination" class="form-label">{{ __('messages.Destination') }} <span class="text-danger">*</span></label>
            <select name="destination" id="destination" class="form-control select2">
                <option value="">{{ __('messages.Select Destination') }} </option>
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
        <div class="form-group mb-3">
            <label for="client_id" class="form-label">{{ __('messages.Select Client') }} <span class="text-danger">*</span></label>
            <select name="client_id" id="client_id" class="form-control select2">
                <option value="">{{ __('messages.Select Client') }} </option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->business_name ??  $client->email}}
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="payer" class="form-label">{{ __('messages.Who Pays Load') }} <span class="text-danger">*</span></label>
            <select name="payer" id="payer" class="form-control">
                <option value="client" {{ old('payer') == 'client' ? 'selected' : '' }}>{{ __('messages.Client') }}</option>
                <option value="another_party" {{ old('payer') == 'another_party' ? 'selected' : '' }}>GEMCO</option>
            </select>
            @error('payer')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>
    
        <div class="form-group mb-3">
            <label for="equipment_type" class="form-label">{{ __('messages.Equipment Type') }} <span class="text-danger">*</span></label>
            <select name="equipment_type" id="equipment_type" class="form-control">
                <option value="">{{ __('messages.Select Equipment') }} </option>
                <option value="53' Truck" {{ old('equipment_type') == "53' Truck" ? 'selected' : '' }}>53' Truck</option>
                <option value="48' Truck" {{ old('equipment_type') == "48' Truck" ? 'selected' : '' }}>48' Truck</option>
                <option value="48' Flatbed" {{ old('equipment_type') == "48' Flatbed" ? 'selected' : '' }}>48' Flatbed</option>
                <option value="53' R" {{ old('equipment_type') == "53' R" ? 'selected' : '' }}>53' R</option>
                <option value="Reefer" {{ old('equipment_type') == "Reefer" ? 'selected' : '' }}>Reefer</option>
            </select>
            <div id="reefer_temperature_field" style="display: none;">
                <label for="reefer_temperature">Required Temperature (°F):</label>
                <input type="number" name="reefer_temperature" id="reefer_temperature" class="form-control" placeholder="Enter temperature" value="{{ old('reefer_temperature') }}">
            </div>
            @error('equipment_type')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>

        <!-- <div class="form-group mb-3">
            <label for="trailer_number" class="form-label">{{ __('messages.Trailer Number') }} </label>
            <input type="text" id="trailer_number" name="trailer_number" class="form-control" value="{{old('trailer_number')}}">
            @error('trailer_number')
                    {{ $message }}
                @enderror
        </div> -->
       <div class="form-group mb-3">
            <label for="port_of_entry" class="form-label">{{ __('messages.Port of Entry') }}</label>
            <select id="port_of_entry" name="port_of_entry" class="form-select">
                <option value="Nuevo Laredo, Tamps." {{ old('port_of_entry') == 'Nuevo Laredo, Tamps.' ? 'selected' : '' }}>
                    Nuevo Laredo, Tamps.
                </option>
                <option value="Colombia, N.L." {{ old('port_of_entry') == 'Colombia, N.L.' ? 'selected' : '' }}>
                    Colombia, N.L.
                </option>
            </select>
            @error('port_of_entry')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
        <div style="display: flex; column-gap: 10px; padding-bottom: 10px;"> <label for="weight" class="form-label">{{ __('messages.Weight') }} </label> <select name="weight_unit" id="weight_unit" class="form-select form-select-sm" style="max-width: 80px;">
            <option value="kg" {{ old('weight_unit') == 'kg' ? 'selected' : '' }}>kg</option>
            <option value="lbs" {{ old('weight_unit') == 'lbs' ? 'selected' : '' }}>lbs</option>
        </select>     </div>
            <input type="text" name="weight" id="weight" class="form-control"  value="{{old('weight')}}">
            @error('weight')
                    {{ $message }}
                @enderror
        </div>

        <!-- <div class="form-group mb-3">
            <label for="delivery_deadline" class="form-label">{{ __('messages.Delivery Deadline') }} <span class="text-danger">*</span></label>
            <input type="date" id="delivery_deadline" name="delivery_deadline" class="form-control" value="{{old('delivery_deadline')}}">
            @error('delivery_deadline')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="schedule" class="form-label">Schedule Date</label>
            <input type="datetime-local" id="schedule" name="schedule" class="form-control" value="{{ old('schedule', date('Y-m-d\T09:00')) }}">
            @error('schedule')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>
         -->

        <div class="form-group mb-3 position-relative">
            <label for="schedule" class="form-label">{{ __('messages.Schedule Date') }}</label>

            <div class="input-group">
            <input type="text" id="schedule" name="schedule" class="form-control"
       value="{{ old('schedule') ? \Carbon\Carbon::createFromFormat('Y-m-d H:i', old('schedule'))->format('M. j, Y H:i') : now()->format('M. j, Y 09:00') }}"
       placeholder="Select date & time" readonly>

       <button type="button" id="calendar-trigger" class="input-group-text" style="cursor: pointer;">
                    <i class="bi bi-calendar"></i> {{-- Replace with your preferred icon if needed --}}
                </button>
                </div>

            @error('schedule')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
       

        <div class="form-group mb-3 position-relative">
            <label for="delivery_deadline" class="form-label">
                {{ __('messages.Delivery Deadline') }} <span class="text-danger">*</span>
            </label>
            <div class="input-group">
            <input type="text" id="delivery_deadline" name="delivery_deadline" class="form-control"
                value="{{ old('delivery_deadline') ? \Carbon\Carbon::createFromFormat('Y-m-d', old('delivery_deadline'))->format('M. j, Y') : now()->format('M. j, Y') }}"
                placeholder="Select a date" readonly>
                <button type="button" id="de-calendar-trigger" class="input-group-text" style="cursor: pointer;">
                    <i class="bi bi-calendar"></i> {{-- Replace with your preferred icon if needed --}}
                </button>
                </div>
            @error('delivery_deadline')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="customer_po" class="form-label">{{ __('messages.Customer PO / Reference Number') }} </label>
            <input type="text" id="customer_po" name="customer_po" class="form-control" value="{{old('customer_po')}}">
            @error('customer_po')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_hazmat" id="is_hazmat" class="form-check-input" value="1" {{ old('is_hazmat') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_hazmat">{{ __('messages.HazMat (Hazardous)') }} </label>
            @error('is_hazmat')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_inbond" id="is_inbond" class="form-check-input" value="1" {{ old('is_inbond') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_inbond">{{ __('messages.Inbond') }} </label>
            @error('is_inbond')
                <div class="text-danger">
                     {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn_primary_color">{{ __('messages.Create Load') }} </button>
    </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
    
$(document).ready(function() {
   
    $('#supplier_id').select2({
            placeholder: "{{ __('messages.Select a Supplier') }}",
            allowClear: true
        });

        $('#origin').select2({
            placeholder: "{{ __('messages.Select Origin') }}",
            allowClear: true
        });
        $('#destination').select2({
            placeholder: "{{ __('messages.Select Destination') }}",
            allowClear: true
        });
        $('#client_id').select2({
            placeholder: "{{ __('messages.Select Destination') }}",
            allowClear: true
        });
        $('#origin, #destination, #client_id').on('select2:open', function() {
            $('.select2-results__options').scrollTop(0);
        });
        function toggleTemperatureField() {
        var equipmentType = $('#equipment_type').val();
        var $reeferField = $('#reefer_temperature_field');
        var $reeferInput = $('#reefer_temperature');

        if (equipmentType === 'Reefer') {
            $reeferField.show();
            $reeferInput.prop('disabled', false);
        } else {
            $reeferField.hide();
            $reeferInput.val('').prop('disabled', true);
        }
    }

    // Initialize the visibility based on the current selection
    toggleTemperatureField();

    // Add event listener to handle changes in the equipment type selection
    $('#equipment_type').change(function() {
        toggleTemperatureField();
    });
});



</script>

@endsection