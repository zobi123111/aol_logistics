@section('title', 'Supplier Service')
{{-- @section('sub-title', 'Supplier Service') --}}
@section('sub-title', GoogleTranslate::trans('Supplier Service', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('services.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }}</a>

    </div>
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}
    </div>
    @endif

    <div class="card card-container">
        <div class="card-body">
        <form action="{{ route('services.store', $supplier->id) }}" method="POST">
            @csrf

            <div class="form-group mb-3 mt-3">
                <label for="origin" class="form-label"> {{ GoogleTranslate::trans('Origin', app()->getLocale()) }} <span class="text-danger">*</span></label>
                <select name="origin" id="origin" class="form-control">
                    <option value="">Select Origin</option>
                    @foreach($origins as $origin)
                        <option value="{{ $origin->id }}" {{ old('origin') == $origin->id ? 'selected' : '' }}>
                            {{ $origin->street }}, {{ $origin->city }}, {{ $origin->state }}, {{ $origin->zip }}, {{ $origin->country }}
                        </option>
                    @endforeach
                </select>
                @error('origin')
                    <div class="text-danger">
                        {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                    </div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="destination" class="form-label"> {{ GoogleTranslate::trans('Destination', app()->getLocale()) }} <span class="text-danger">*</span></label>
                <select name="destination" id="destination" class="form-control">
                    <option value="">Select Destination</option>
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" {{ old('destination') == $destination->id ? 'selected' : '' }}>
                            {{ $destination->street }}, {{ $destination->city }}, {{ $destination->state }}, {{ $destination->zip }}, {{ $destination->country }}
                        </option>
                    @endforeach
                </select>
                @error('destination')
                    <div class="text-danger">
                        {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                    </div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="cost" class="form-label"> {{ GoogleTranslate::trans('Cost (USD)', app()->getLocale()) }} <span class="text-danger">*</span></label>
                <input type="text" name="cost" id="cost" class="form-control" step="0.01" value="{{ old('cost') }}">
                @error('cost')
                    <div class="text-danger">
                        {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn_primary_color"> {{ GoogleTranslate::trans('Create Service', app()->getLocale()) }} </button>

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