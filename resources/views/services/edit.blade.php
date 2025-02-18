@section('title', 'Supplier Service')
@section('sub-title', 'Supplier Service')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('services.index',  $supplierId) }}" class="btn btn-primary create-button btn_primary_color" id="createUser"><i class="bi bi-arrow-left-circle-fill"> </i>back</a>
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

        <!-- Origin -->
        <div class="form-group mb-3 mt-3">
            <label for="origin" class="form-label">Origin <span class="text-danger">*</span></label>
            <select name="origin" class="form-control">
                <option value="">Select Origin</option>
                <option value="Chicago, IL" {{ old('origin', $service->origin) == 'Chicago, IL' ? 'selected' : '' }}>Chicago, IL</option>
                <option value="New York, NY" {{ old('origin', $service->origin) == 'New York, NY' ? 'selected' : '' }}>New York, NY</option>
                <option value="Los Angeles, CA" {{ old('origin', $service->origin) == 'Los Angeles, CA' ? 'selected' : '' }}>Los Angeles, CA</option>
                <option value="Houston, TX" {{ old('origin', $service->origin) == 'Houston, TX' ? 'selected' : '' }}>Houston, TX</option>
            </select>
            @error('origin')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <!-- Destination -->
        <div class="form-group mb-3">
            <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
            <select name="destination" class="form-control">
                <option value="">Select Destination</option>
                <option value="Miami, FL" {{ old('destination', $service->destination) == 'Miami, FL' ? 'selected' : '' }}>Miami, FL</option>
                <option value="Seattle, WA" {{ old('destination', $service->destination) == 'Seattle, WA' ? 'selected' : '' }}>Seattle, WA</option>
                <option value="Denver, CO" {{ old('destination', $service->destination) == 'Denver, CO' ? 'selected' : '' }}>Denver, CO</option>
                <option value="Atlanta, GA" {{ old('destination', $service->destination) == 'Atlanta, GA' ? 'selected' : '' }}>Atlanta, GA</option>
            </select>
            @error('destination')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <!-- Cost -->
        <div class="form-group mb-3">
            <label for="cost" class="form-label">Cost (USD) <span class="text-danger">*</span></label>
            <input type="text" step="0.01" name="cost" class="form-control" value="{{ old('cost', $service->cost) }}">
            @error('cost')
                <div class="text-danger">{{ $message }}</div>
            @enderror  
        </div>

        <button type="submit" class="btn btn-primary btn_primary_color">Update Service</button>
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