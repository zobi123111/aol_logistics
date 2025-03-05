@section('title', 'Update Truck Details')
{{-- @section('sub-title', 'Update Truck Details') --}}
@section('sub-title', __('messages.Loads'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
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
        <form action="{{ route('loads.updateTruckDetails', $load->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="truck_number" class="form-label">{{ __('messages.truck_number') }}</label>
        <input type="text" class="form-control" name="truck_number" value="{{ old('truck_number', $load->truck_number) }}">
        @error('truck_number')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="driver_name" class="form-label">{{ __('messages.driver_name') }}</label>
        <input type="text" class="form-control" name="driver_name" value="{{ old('driver_name', $load->driver_name) }}">
        @error('driver_name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="driver_contact_no" class="form-label">{{ __('messages.driver_contact_no') }}</label>
        <input type="text" class="form-control" name="driver_contact_no" value="{{ old('driver_contact_no', $load->driver_contact_no) }}">
        @error('driver_contact_no')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Show Existing Uploaded Documents -->
    <div class="mb-3">
        <label class="form-label">{{ __('messages.uploaded_documents') }}</label>
        <ul class="list-group">
            @foreach($load->documents as $index => $document)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ asset('storage/' . $document->path) }}" target="_blank">
                        {{ __('messages.view_document') }} {{ $index + 1 }}
                    </a>
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal({{$document->id}}, '{{ route('loads.deleteDocument', encode_id($document->id)) }}')">
                        <i class="fas fa-times"></i> {{ __('messages.delete') }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-3">
        <label for="documents" class="form-label">{{ __('messages.upload_documents') }}</label>
        <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.jpg,.png,.docx,.xlsx">
        @error('documents.*') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary create-button btn_primary_color">{{ __('messages.update') }}</button>
</form>

        </div>
    </div>
</div>

<form method="POST" id="deleteRoleFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{ __('messages.Delete') }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    {{ __('messages.delete_document_confirmation') }}
                                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">{{ __('messages.Close') }} </button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color">{{ __('messages.Delete') }} </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js_scripts')

<script>
$(document).ready(function() {

});
function showDeleteModal(id, deleteUrl) {
    $('#deleteRoleFormId').attr('action', deleteUrl);
    let form = document.getElementById("deleteRoleFormId");
    new bootstrap.Modal(document.getElementById('deleteRoleForm')).show();
}
</script>

@endsection