@section('title', 'Origins')
{{-- @section('sub-title', 'Origins') --}}
@section('sub-title', __('messages.Origins'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('origins.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser"><i
                class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
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
        <form action="{{ route('origins.store') }}" method="POST">
        @csrf
        <div class="mb-3 mt-3">
            <label for="name" class="form-label"> {{ __('messages.Name for Address') }} </label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" >
            @error('name')
                {{ $message }}
            @enderror
        </div>
        <div class="mb-3">
            <label for="street" class="form-label"> {{ __('messages.Street') }} </label>
            <input type="text" name="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}" >
            @error('street')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="city" class="form-label"> {{ __('messages.City') }} </label>
            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" >
            @error('city')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="state" class="form-label"> {{ __('messages.State') }} </label>
            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" >
            @error('state')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="zip" class="form-label"> {{ __('messages.ZIP Code') }} </label>
            <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror" value="{{ old('zip') }}" >
            @error('zip')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="country" class="form-label"> {{ __('messages.Country') }} </label>
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
                 
            <button type="submit" class="btn btn-primary create-button btn_primary_color"> {{ __('messages.Submit') }} </button>
            </form>
        </div>
    </div>
</div>
@endsection
