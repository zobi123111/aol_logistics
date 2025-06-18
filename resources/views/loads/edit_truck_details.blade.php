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

    <!-- <div class="mb-3">
        <label for="truck_number" class="form-label">{{ __('messages.truck_number') }}</label>
        <input type="text" class="form-control" name="truck_number" value="{{ old('truck_number', $load->truck_number) }}">
        @error('truck_number')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="supplier_id" class="form-label">{{ __('messages.supplier') }}</label>
        <select name="supplier_id" class="form-select">
            <option value="">{{ __('messages.supplier') }}</option>
           @foreach($assignedSuppliers as $assigned)
            @if ($assigned->supplier)
                <option value="{{ $assigned->supplier->id }}" {{ old('supplier_id', $load->truck_supplier_id) == $assigned->supplier->id ? 'selected' : '' }}>
                    {{ $assigned->supplier->company_name }}
                </option>
            @endif
        @endforeach
        </select>
        @error('supplier_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div> -->

    <div class="mb-3">
    <label for="supplier_id" class="form-label">{{ __('messages.supplier') }}</label>
    <select name="supplier_id" id="supplier_id" class="form-select">
        <option value="">{{ __('messages.select_supplier') }}</option>
        @foreach($assignedSuppliers as $assigned)
            @if ($assigned->supplier)
                <option value="{{ $assigned->supplier->id }}" 
                    {{ old('supplier_id', $load->truck_supplier_id) == $assigned->supplier->id ? 'selected' : '' }}>
                    {{ $assigned->supplier->company_name }}
                </option>
            @endif
        @endforeach
    </select>
    @error('supplier_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @if ($assignedSuppliers->isEmpty())
    <small class="d-block mt-1">{{ __('messages.No supplier assigned to this load.') }}
</small>
@endif
</div>


<div class="mb-3">
    <label for="truck_number" class="form-label">{{ __('messages.truck_number') }}</label>
    <select name="truck_number" id="truck_number" class="form-select">
        <option value="{{ old('truck_number', $load->truck_number) }}">{{ old('truck_number', $load->truck_number) }}</option>
    </select>
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
<!-- 
    <div class="mb-3">
        <label for="documents" class="form-label">{{ __('messages.upload_documents') }}</label>
        <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.jpg,.png,.docx,.xlsx">
        @error('documents.*') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div> -->
    <!-- <div class="mb-3">
    <label for="documents" class="form-label">{{ __('messages.upload_documents') }}</label>

    <div id="dropzone" class="border border-dashed rounded p-4 text-center" style="cursor: pointer;">
        <p class="mb-0">{{ __('messages.drag_drop_or_click') }}</p>
        <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.png,.docx,.xlsx" class="d-none">
    </div>

    @error('documents.*') 
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div> -->
<div class="mb-3">
    <label class="form-label">{{ __('messages.upload_documents') }}</label>

    <div class="drop-zone border border-dashed rounded p-4 text-center" id="dropZone-documents" style="cursor: pointer;">
        <i class="fas fa-upload fa-2x mb-2"></i><br>
        <span id="dropZoneText-documents">{{ __('messages.Drag & drop or click to upload') }}</span>
        <input type="file" name="documents[]" id="fileInput-documents" multiple class="d-none" accept=".pdf,.jpg,.png,.docx,.xlsx">
    </div>

    <div class="file-names mt-2" id="fileNames-documents"></div>

    <small class="text-muted">{{ __('messages.You can upload multiple legal documents') }}</small>

    @error('documents')
        <div class="text-danger">{{ $message }}</div>
    @enderror

    @foreach ($errors->get('documents.*') as $message)
        <div class="text-danger">{{ $message[0] }}</div>
    @endforeach
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
$(document).ready(function () {
    const fileFields = ['documents'];
    const fileStore = {};

    fileFields.forEach(field => {
        fileStore[field] = [];

        const dropZone = document.getElementById(`dropZone-${field}`);
        const fileInput = document.getElementById(`fileInput-${field}`);
        const fileNames = document.getElementById(`fileNames-${field}`);
        const dropZoneText = document.getElementById(`dropZoneText-${field}`);

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-light');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-light');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-light');
            const files = Array.from(e.dataTransfer.files);
            fileStore[field] = fileStore[field].concat(files);
            updateFileDisplay(field);
        });

        fileInput.addEventListener('change', () => {
            const files = Array.from(fileInput.files);
            fileStore[field] = fileStore[field].concat(files);
            updateFileDisplay(field);
        });

        function updateFileDisplay(field) {
            const container = document.getElementById(`fileNames-${field}`);
            const textElement = document.getElementById(`dropZoneText-${field}`);
            const files = fileStore[field];

            if (files.length) {
                container.innerHTML = files.map((f, i) => `
                    <div class="d-flex align-items-center justify-content-between border p-1 mb-1 rounded">
                        <span>📎 ${f.name}</span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeFile('${field}', ${i})">✖</button>
                    </div>
                `).join('');
                textElement.textContent = "Files Selected:";
            } else {
                container.innerHTML = '';
                textElement.textContent = "Drag & drop or click to upload";
            }

            const dataTransfer = new DataTransfer();
            files.forEach(f => dataTransfer.items.add(f));
            fileInput.files = dataTransfer.files;
        }

        window.removeFile = (field, index) => {
            fileStore[field].splice(index, 1);
            updateFileDisplay(field);
        };
    });
    $('#supplier_id').on('change', function () {
            let supplierId = $(this).val();

            if (supplierId) {
                $.ajax({
                    url: `/supplier/${supplierId}/trucks`,
                    type: 'GET',
                    success: function (response) {
                        $('#truck_number').empty();
                        $('#truck_number').append(`<option value="">@lang('messages.select_truck')</option>`);

                        response.forEach(function (truck) {
                            $('#truck_number').append(`<option value="${truck.truck_number}">${truck.truck_number}</option>`);
                        });
                    }
                });
            } else {
                $('#truck_number').html('<option value="">@lang("messages.select_truck")</option>');
            }
        });
});
function showDeleteModal(id, deleteUrl) {
    $('#deleteRoleFormId').attr('action', deleteUrl);
    let form = document.getElementById("deleteRoleFormId");
    new bootstrap.Modal(document.getElementById('deleteRoleForm')).show();
}
</script>

@endsection