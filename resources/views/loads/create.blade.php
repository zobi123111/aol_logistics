@section('title', 'Supplier Equipment')
@section('sub-title', 'Supplier Equipment')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> back</a>
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

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>    
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="card card-container">
        <div class="card-body">
        <form action="{{ route('loads.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3 mt-3">
                <label for="origin" class="form-label">Origin<span class="text-danger">*</span></label>
                <select name="origin" id="origin" class="form-control">
                    <option value="">Select Origin</option>
                    <option value="Chicago, IL" {{ old('origin') == 'Chicago, IL' ? 'selected' : '' }}>Chicago, IL</option>
                    <option value="New York, NY" {{ old('origin') == 'New York, NY' ? 'selected' : '' }}>New York, NY</option>
                    <option value="Los Angeles, CA" {{ old('origin') == 'Los Angeles, CA' ? 'selected' : '' }}>Los Angeles, CA</option>
                    <option value="Houston, TX" {{ old('origin') == 'Houston, TX' ? 'selected' : '' }}>Houston, TX</option>
                </select>
                @error('origin')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="destination" class="form-label">Destination<span class="text-danger">*</span></label>
                <select name="destination" id="destination" class="form-control">
                    <option value="">Select Destination</option>
                    <option value="Miami, FL" {{ old('destination') == 'Miami, FL' ? 'selected' : '' }}>Miami, FL</option>
                    <option value="Seattle, WA" {{ old('destination') == 'Seattle, WA' ? 'selected' : '' }}>Seattle, WA</option>
                    <option value="Denver, CO" {{ old('destination') == 'Denver, CO' ? 'selected' : '' }}>Denver, CO</option>
                    <option value="Atlanta, GA" {{ old('destination') == 'Atlanta, GA' ? 'selected' : '' }}>Atlanta, GA</option>
                </select>
                @error('destination')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

        <div class="form-group mb-3">
            <label for="payer" class="form-label">Who Pays Load<span class="text-danger">*</span></label>
              <select name="payer" id="payer" class="form-control">
                    <option value="">Select Payer</option>
                    <option value="client" {{ old('payer') == 'client' ? 'selected' : '' }}>Client directly</option>
                    <option value="another_party" {{ old('payer') == 'another_party' ? 'selected' : '' }}>Another party will pay for the load</option>
                </select>
                @error('payer')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>

        <!-- <div class="form-group mb-3">
            <label for="equipment_type" class="form-label">Equipment Type</label>
            <select name="equipment_type" id="equipment_type" class="form-control">
                <option value="">Select Equipment</option>
                <option value="53' Truck" {{ old('equipment_type') == "53' Truck" ? 'selected' : '' }}>53' Truck</option>
                <option value="48' Truck" {{ old('equipment_type') == "48' Truck" ? 'selected' : '' }}>48' Truck</option>
                <option value="48' Flatbed" {{ old('equipment_type') == "48' Flatbed" ? 'selected' : '' }}>48' Flatbed</option>
                <option value="53' R" {{ old('equipment_type') == "53' R" ? 'selected' : '' }}>53' R</option>
                </select>
                @error('equipment_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div> -->

        <div class="form-group mb-3">
            <label for="weight" class="form-label">Weight<span class="text-danger">*</span></label>
            <input type="text" name="weight" id="weight" class="form-control"  value="{{old('weight')}}">
            @error('equipment_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>

        <div class="form-group mb-3">
            <label for="delivery_deadline" class="form-label">Delivery Deadline<span class="text-danger">*</span></label>
            <input type="date" id="delivery_deadline" name="delivery_deadline" class="form-control" value="{{old('delivery_deadline')}}">
            @error('equipment_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>

        <div class="form-group mb-3">
            <label for="customer_po" class="form-label">Customer PO / Reference Number<span class="text-danger">*</span></label>
            <input type="text" id="customer_po" name="customer_po" class="form-control" value="{{old('customer_po')}}">
            @error('customer_po')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_hazmat" id="is_hazmat" class="form-check-input" value="1" {{ old('is_hazmat') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_hazmat">HazMat (Hazardous)</label>
            @error('is_hazmat')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_inbond" id="is_inbond" class="form-check-input" value="1" {{ old('is_inbond') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_inbond">Inbond Load</label>
            @error('is_inbond')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn_primary_color">Create Load</button>
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