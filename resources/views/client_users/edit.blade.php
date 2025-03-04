@extends('layout.app')

@section('title', 'Client Customer Service Executve')
{{-- @section('sub-title', 'Client Customer Service Executve') --}}
@section('sub-title', __('messages.Client Customer Service Executive'))

@section('content')
<div class="main_cont_outer">
    <!-- Back Button -->
    <div class="create_btn">
        <a href="{{ route('client_users.index', encode_id($master_client)) }}" class="btn btn-primary create-button btn_primary_color">
            <i class="bi bi-arrow-left-circle-fill"></i>  {{ __('messages.Back') }} </a>
    </div>

    <!-- Success Message -->
    @if(session()->has('message'))
        <div id="successMessage" class="alert alert-success fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> 
            {{ session('message') }}
        </div>
    @endif

    <!-- Client Form Card -->
    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('client_users.update', [encode_id($client->id), encode_id($master_client)] )  }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Client First Name -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.First Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="client_Fname" class="form-control" value="{{ old('client_Fname', $client->fname) }}">
                    @error('client_Fname')
                        <div class="text-danger">
                               {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Client Last Name -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Last Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="client_Lname" class="form-control" value="{{ old('client_Lname', $client->lname) }}">
                    @error('client_Lname')
                        <div class="text-danger">
                               {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Client Email -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" >
                    @error('email')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Profile Photo Upload -->
                <!-- <div class="mb-3">
                <div id="current-profile-photo" class="mt-2"><div class="image-cont" style="position:relative;"><label>Current Profile Photo</label><br><img src="{{ asset('storage/' . $client->profile_photo) }}" width="100" height="100" class="rounded-circle" alt="Profile Photo"><button type="button" class="btn btn-danger btn-sm position-absolute remove-photo">x</button></div>
                        </div>
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control">
                    @error('profile_photo')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Save Client') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js_scripts')
<script>
$(document).ready(function() {
    // Hide success message after 3 seconds
    setTimeout(function() {
        $('#successMessage').fadeOut('slow');
    }, 3000);
});
</script>
@endsection
