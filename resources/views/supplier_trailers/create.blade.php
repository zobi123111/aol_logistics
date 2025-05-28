@section('title', __('messages.Truck Number'))
@section('sub-title', __('messages.Truck Number'). ' | Company: ' . $supplier->company_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_trailers.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
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
    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('supplier_trailers.store', encode_id($supplier->id)) }}" method="POST">
                @csrf

            <div class="form-group mb-3">
            <label for="trailer_num" class="form-label">{{__('messages.Truck Number')}}</label>
            <input 
                type="text" 
                class="form-control" 
                id="trailer_num" 
                name="trailer_num" 
                value="{{ old('trailer_num') }}" 
            >
            @error('trailer_num')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Save') }} </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>

</script>

@endsection