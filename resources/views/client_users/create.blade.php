@section('title', 'Client Customer Service Executve')
{{-- @section('sub-title', 'Client Customer Service Executve') --}}
@section('sub-title', __('messages.Client'). ' | Business: ' . $client->business_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('client_users.index', encode_id($client_id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
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
            <form action="{{ route('client_users.store', $client_id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Client First Name -->
                <div class="mb-3 mt-3">
                    <label class="form-label">{{ __('messages.First Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="client_Fname" class="form-control" value="{{ old('client_Fname') }}">
                    @error('client_Fname')
                        <div class="text-danger">
                                  {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Client Last Name -->
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.Last Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="client_Lname" class="form-control" value="{{ old('client_Lname') }}">
                    @error('client_Lname')
                        <div class="text-danger">
                                  {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Client Email -->
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.Email') }} <span class="text-danger">*</span></label>
                    <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger">
                              {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.User Role') }} <span class="text-danger">*</span></label>
                    <select name="user_role" class="form-select">
                        <option value="cl_master_client" {{ old('user_role') == 'cl_master_client' ? 'selected' : '' }}> Master Client</option>
                        <option value="cl_customer_service_executive" {{ old('user_role') == 'cl_customer_service_executive' ? 'selected' : '' }}> Customer Service Executive</option>
                    </select>
                    @error('user_role')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror           
                </div>

                <!-- Client Password -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.Password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                        @error('password')
                            <div class="text-danger">
                                  {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.Confirm Password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
                        @error('confirm_password')
                            <div class="text-danger">
                                  {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Profile Photo Uploads -->
                <!-- <div class="mb-3">
                    <label for="profile_photo" class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                    @error('profile_photo')
                        {{ $message }}
                    @enderror
                </div> -->

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn_primary_color">{{ __('messages.Save Client') }} </button>
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