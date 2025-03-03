@section('title', 'Supplier Service')
{{-- @section('sub-title', 'Supplier Service') --}}
@section('sub-title', __('messages.Supplier Service'))
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

                <!-- Origin -->
                <div class="form-group mb-3 mt-3">
                    <label for="origin" class="form-label"> {{ __('messages.Origin') }}  <span class="text-danger">*</span></label>
                    <select name="origin" class="form-control">
                        <option value="">Select Origin</option>
                        @foreach($origins as $origin)
                            <option value="{{ $origin->id }}" {{ old('origin', $service->origindata->id) == $origin->id ? 'selected' : '' }}>
                                {{ $origin->street }}, {{ $origin->city }}, {{ $origin->state }}, {{ $origin->country }}
                            </option>
                        @endforeach
                    </select>
                    @error('origin')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror  
                </div>

                <!-- Destination -->
                <div class="form-group mb-3">
                    <label for="destination" class="form-label"> {{ __('messages.Destination') }} <span class="text-danger">*</span></label>
                    <select name="destination" class="form-control">
                        <option value="">Select Destination</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ old('destination', $service->destinationdata->id) == $destination->id ? 'selected' : '' }}>
                                {{ $destination->street }}, {{ $destination->city }}, {{ $destination->state }}, {{ $destination->country }}
                            </option>
                        @endforeach
                    </select>
                    @error('destination')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror  
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
    
    });

</script>

@endsection