@section('title', 'Roles')
{{-- @section('sub-title', 'Roles') --}}
@section('sub-title', __('messages.Role'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('roles.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i> {{ __('messages.Back') }}
        </a>
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
    <div class="card card-container role-container">
    <div class="card-body">
    <form action="{{ route('roles.update', ['role' => encode_id($role->id)]) }}" method="POST">
    @csrf
    @method('PUT') 
    <div class="mb-3 mt-3">
        <div class="form-group">
            <label for="user_type" class="form-label"> {{ __('messages.User Type') }} <span class="text-danger">*</span></label>
            <input type="text" name="user_type" id="user_type" class="form-control" value="{{ old('user_type', $role->userType->name) }}" disabled>
            <div id="user_type_error" class="text-danger error_e"></div>
        </div>
    </div>
    <div class="mb-3">
    <div class="form-group">
        <label for="role_name" class="form-label"> {{ __('messages.Role Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="role_name" class="form-control" value="{{ old('role_name', $role->role_name) }}">
        <div id="role_name_error" class="text-danger error_e"></div>
    </div>
    
        @error('role_name')
                <div class="text-danger">
                    {{ $message }} 
                </div>
            @enderror
        </div>

    <div class="mb-3">
        <label class="form-label"> {{ __('messages.Pages and their Modules') }} : </label>
        <div id="pages-modules-container">
            @foreach ($pages as $page)
            <div class="row mb-3">
                <legend class="col-form-label col-sm-2 pt-0">{{ ucfirst(__('messages.'.$page->name)) }}</legend>
                <div class="col-sm-10">
                    <div class="module_cont">
                        @foreach ($page->modules as $module)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="module_ids[{{ $page->id }}][]" value="{{ $module->id }}" id="module-{{ $module->id }}"
                                        {{ in_array($module->id, $currentModules) ? 'checked' : '' }}>
                                <label class="form-check-label" for="module-{{ $module->id }}">
                                    {{-- {{ $module->name }}  --}}
                                    {{ ucfirst(__('messages.'.$module->name)) }}
                                </label>
                                <br>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @error('module_ids')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
    </div>
    @if( Auth::user()->is_dev)
    <button type="submit" class="btn btn-primary create-button btn_primary_color"> {{ __('messages.Submit') }} </button>
    @endif
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