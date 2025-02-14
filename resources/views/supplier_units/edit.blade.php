@section('title', 'Supplier User')
@section('sub-title', 'Supplier User')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_users.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color" id="createUser"><i class="bi bi-arrow-left-circle-fill"> </i>back</a>
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
    <form action="{{ route('supplier_units.update', [$supplier->id, $unit->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="unit_type" class="form-label">Unit Type<span class="text-danger">*</span></label>
            <select name="unit_type" class="form-control" >
                <option value="">Select Unit Type</option>
                <option value="53' Truck" {{ old('unit_type', $unit->unit_type) == "53' Truck" ? 'selected' : '' }}>53' Truck</option>
                <option value="48' Truck" {{ old('unit_type', $unit->unit_type) == "48' Truck" ? 'selected' : '' }}>48' Truck</option>
                <option value="48' Flatbed" {{ old('unit_type', $unit->unit_type) == "48' Flatbed" ? 'selected' : '' }}>48' Flatbed</option>
                <option value="53' R" {{ old('unit_type', $unit->unit_type) == "53' R" ? 'selected' : '' }}>53' R</option>
            </select>
            @error('unit_type')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <div class="form-group mb-3">
            <label for="unit_number" class="form-label">Unit Number<span class="text-danger">*</span></label>
            <input type="text" name="unit_number" class="form-control" value="{{ old('unit_number', $unit->unit_number) }}" >
            @error('unit_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <div class="form-group mb-3">
            <label for="license_plate" class="form-label">License Plate<span class="text-danger">*</span></label>
            <input type="text" name="license_plate" class="form-control" placeholder="Example: AJ-53-AJ" value="{{ old('license_plate', $unit->license_plate) }}" >
            @error('license_plate')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <div class="form-group mb-3">
            <label for="state" class="form-label">State<span class="text-danger">*</span></label>
            <input type="text" name="state" class="form-control" value="{{ old('state', $unit->state) }}" >
            @error('state')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <button type="submit" class="btn btn-primary btn_primary_color">Update Unit</button>
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