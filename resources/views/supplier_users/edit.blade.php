@section('title', 'Supplier User')
{{-- @section('sub-title', 'Supplier User') --}}
@section('sub-title', GoogleTranslate::trans('Supplier User', app()->getLocale()))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('supplier_users.index',  encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }}
        </a>
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
            <form action="{{ route('supplier_users.update', [encode_id($supplier->id), encode_id($user->id)]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3 mt-3">
                    <label for="firstname" class="form-label"> {{ GoogleTranslate::trans('First Name', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $user->fname) }}">
                    @error('firstname') 
                       <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="lastname" class="form-label"> {{ GoogleTranslate::trans('Last Name', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" class="form-control" value="{{ old('lastname', $user->lname) }}">
                    @error('lastname') 
                       <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="form-label"> {{ GoogleTranslate::trans('Email', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                    @error('email') 
                       <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('User Role', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <select name="user_role" class="form-select">
                        <option value="customer_service_executive" {{ old('user_role',  $user->roledata->role_slug) == 'customer_service_executive' ? 'selected' : '' }}>Customer Service Executive</option>
                        <option value="accounting_user" {{ old('user_role',  $user->roledata->role_slug) == 'accounting_user' ? 'selected' : '' }}>Accounting User</option>
                    </select>
                    @error('user_role') 
                       <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary btn_primary_color"> {{ GoogleTranslate::trans('Update User', app()->getLocale()) }} </button>
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