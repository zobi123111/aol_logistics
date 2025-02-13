@section('title', 'Supplier User')
@section('sub-title', 'Supplier User')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_users.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
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
<!-- 
    @if ($errors->any())
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
        <form action="{{ route('supplier_users.store', $supplier->id) }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="firstname" class="form-label">First Name<span class="text-danger">*</span></label>
            <input type="text" name="firstname" class="form-control" value="{{ old('firstname') }}">
            @error('firstname')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
        </div>
        <div class="form-group mb-3">
            <label for="lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
            <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}">
            @error('lastname')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror        </div>
        <div class="form-group mb-3">
            <label for="email" class="form-label">Email<span class="text-danger" value="{{ old('email') }}">*</span></label>
            <input type="email" name="email" class="form-control">
            @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror        </div>
        <div class="mb-3">
            <label class="form-label">User Role<span class="text-danger">*</span></label>
            <select name="user_role" class="form-select">
                <option value="customer_service_executive"
                    {{ old('user_role') == 'customer_service_executive' ? 'selected' : '' }}>Customer Service
                    Executive</option>
                <option value="accounting_user" {{ old('user_role') == 'accounting_user' ? 'selected' : '' }}>
                    Accounting User</option>
            </select>
            @error('user_role')
            <div class="text-danger">{{ $message }}</div>
            @enderror           
        </div>
        <div class="form-group mb-3">
            <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control">
            @error('password')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror        </div>
        <div class="form-group mb-3">
            <label for="confirmpassword" class="form-label">Confirm Password<span
                    class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" id="confirmpassword">
            @error('password_confirmation')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror        </div>
        <button type="submit" class="btn btn-primary btn_primary_color">Add User</button>

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