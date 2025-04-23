@extends('layout.app')

@section('title', 'Edit Supplier')
{{-- @section('sub-title', 'Edit Supplier') --}}
@section('sub-title', __('messages.Edit Supplier'). ' | Company: ' . $supplier->company_name)

@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color">
            <i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}
        </a>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif

    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Company Name -->
                <div class="mb-3 mt-3">
                    <label class="form-label">  {{ __('messages.Full and Legal Name of the Company') }} </label><span class="text-danger">*</span>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $supplier->company_name) }}">
                    @error('company_name')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- DBA -->
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.DBA (Nickname)') }} </label><span class="text-danger">*</span>
                    <input type="text" name="dba" class="form-control" value="{{ old('dba', $supplier->dba) }}">
                    @error('dba')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Address Fields -->
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Street Address') }} </label><span class="text-danger">*</span>
                    <input type="text" name="street_address" class="form-control" value="{{ old('street_address', $supplier->street_address) }}">
                    @error('street_address')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">  {{ __('messages.City') }} </label><span class="text-danger">*</span>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city) }}">
                        @error('city')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                    @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">  {{ __('messages.State') }} </label><span class="text-danger">*</span>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $supplier->state) }}">
                        @error('state')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">  {{ __('messages.Zip Code') }} </label><span class="text-danger">*</span>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $supplier->zip_code) }}">
                        @error('zip_code')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Country') }} </label><span class="text-danger">*</span>
                    <select name="country" class="form-select">
                        <option value="USA" {{ old('country', $supplier->country) == 'USA' ? 'selected' : '' }}>United States</option>
                        <option value="Mexico" {{ old('country', $supplier->country) == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                        <option value="Canada" {{ old('country', $supplier->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                        <option value="Other" {{ old('country', $supplier->country) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('country')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Contact Details -->
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Office Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="office_phone" class="form-control" value="{{ old('office_phone', $supplier->office_phone) }}">
                    @error('office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Primary Contact Email') }} </label><span class="text-danger">*</span>
                    <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $supplier->primary_contact_email) }}">
                    @error('primary_contact_email')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Primary Contact Office Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_office_phone" class="form-control" value="{{ old('primary_contact_office_phone', $supplier->primary_contact_office_phone) }}">
                    @error('primary_contact_office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Primary Contact Mobile Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_mobile_phone" class="form-control" value="{{ old('primary_contact_mobile_phone', $supplier->primary_contact_mobile_phone) }}">
                    @error('primary_contact_mobile_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.User Email') }}  <small>  {{ __('messages.(Used for login)') }} </small></label><span class="text-danger">*</span>
                    <input type="email" name="user_email" class="form-control" value="{{ old('user_email', $supplier->user_email) }}">
                    @error('user_email')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.User Office Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_office_phone" class="form-control" value="{{ old('user_office_phone', $supplier->user_office_phone) }}">
                    @error('user_office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.User Mobile Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_mobile_phone" class="form-control" value="{{ old('user_mobile_phone', $supplier->user_mobile_phone) }}">
                    @error('user_mobile_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Password') }} </label><span class="text-danger">*</span>
                    <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                    @error('password')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

             
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Confirm Password') }} </label><span class="text-danger">*</span>
                    <input type="password" name="password_confirmation" class="form-control"
                        value="{{ old('password_confirmation') }}">
                    @error('password_confirmation')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <div class="mb-3">
                    <label class="form-label" for="service_type">  {{ __('messages.Type of Service Authorized') }} </label><span class="text-danger">*</span>
                    <select id="service_type" name="service_type[]" class="form-select select2" multiple>
                        @php
                            $oldServices = old('service_type', $supplier->service_type ?? []);
                            $selectedServices = is_array($oldServices) ? $oldServices : explode(',', $oldServices);
                        @endphp

                        <option value="Land Freight" {{ in_array('Land Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Land Freight') }}
                        </option>
                        <option value="Air Freight" {{ in_array('Air Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Air Freight') }}
                        </option>
                        <option value="Ocean Freight" {{ in_array('Ocean Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Ocean Freight') }}
                        </option>
                    </select>

                    @error('service_type')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Will the supplier invoice in') }} </label><span class="text-danger">*</span>
                    <div>
                        <input type="radio" name="currency" value="USD" id="usd"
                            {{ old('currency', $supplier->currency) == 'USD' ? 'checked' : '' }}> <label for="usd">USD</label>
                        <input type="radio" name="currency" value="MXP" id="mxp"
                            {{ old('currency', $supplier->currency) == 'MXP' ? 'checked' : '' }}> <label for="mxp">MXP</label>
                        <input type="radio" name="currency" value="Both" id="both"
                            {{ old('currency', $supplier->currency) == 'Both' ? 'checked' : '' }}> <label for="both">Both</label>
                    </div>
                    @error('currency')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

            
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Preferred Language') }} </label><span class="text-danger">*</span>
                    <select name="preferred_language" class="form-select">
                        <option value="english" {{ old('preferred_language', $supplier->preferred_language) == 'english' ? 'selected' : '' }}>English</option>
                        <option value="spanish" {{ old('preferred_language', $supplier->preferred_language) == 'spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="french" {{ old('preferred_language', $supplier->preferred_language) == 'french' ? 'selected' : '' }}>French</option>
                        <option value="german" {{ old('preferred_language', $supplier->preferred_language) == 'german' ? 'selected' : '' }}>German</option>
                    </select>
                    @error('preferred_language')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.SCAC Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="scac_number" class="form-control" value="{{ old('scac_number', $supplier->scac_number) }}">
                    @error('scac_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

              

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.CAAT Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="caat_number" class="form-control" value="{{ old('caat_number', $supplier->caat_number) }}">
                    @error('caat_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">  ctpat_number </label><span class="text-danger">*</span>
                    <input type="text" name="ctpat_number" class="form-control" value="{{ old('ctpat_number', $supplier->ctpat_number) }}">
                    @error('ctpat_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="upload-design">
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Uploaded Documents') }} </label>
                    <ul>
                    @if($supplier->supplierdocuments && $decodedDocuments = json_decode($supplier->supplierdocuments, true))
                        @php
                            $filteredDocuments = collect($decodedDocuments)->filter(function($document) {
                                return isset($document['document_type']) && $document['document_type'] === 'documents';
                            });
                        @endphp

                        @if($filteredDocuments->isNotEmpty())
                            <ul>
                                @foreach($filteredDocuments as $index => $document)
                                    <li>
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank">  {{ __('messages.View Document') }} </a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}">  {{ __('messages.Delete') }}  
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>  {{ __('messages.No documents found') }}  </p>
                        @endif
                    @else
                        <p>  {{ __('messages.No documents found') }}  </p>
                    @endif
                    </ul>
                </div>

                <!-- <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Upload New Documents') }} </label>
                    <input type="file" name="document_path[]" class="form-control" multiple>
                    <small class="text-muted">   {{ __('messages.You can upload multiple documents') }} </small>
                    @error('document_path')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    @foreach ($errors->get('document_path.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div> -->
                <div class="mb-3">
    <label class="form-label">{{ __('messages.Legal Documents') }}</label>
    <div class="drop-zone" id="dropZone-document">
        <i class="fas fa-upload"></i>
        <span id="dropZoneText-document">{{ __('messages.Drag & drop or click to upload') }}</span>
        <input type="file" name="document_path[]" id="fileInput-document" multiple>
    </div>
    <div class="file-names" id="fileNames-document"></div>
    <small class="text-muted">{{ __('messages.You can upload multiple legal documents') }}</small>

    @error('document_path')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @foreach ($errors->get('document_path.*') as $message)
        <div class="text-danger">{{ $message[0] }}</div>
    @endforeach
</div>
                </div>

                <!-- SCAC Documents -->
                <div class="upload-design">
          
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Uploaded SCAC Documents') }} </label>

                    <ul>
                    @if($supplier->supplierdocuments && $decodedDocuments = json_decode($supplier->supplierdocuments, true))
                        @php
                            $filteredDocuments = collect($decodedDocuments)->filter(function($document) {
                                return isset($document['document_type']) && $document['document_type'] === 'scac_documents';
                            });
                        @endphp

                        @if($filteredDocuments->isNotEmpty())
                            <ul>
                                @foreach($filteredDocuments as $index => $document)
                                    <li>
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank"> {{ __('messages.View Document') }}</a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}">  {{ __('messages.Delete') }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>  {{ __('messages.No documents found') }} </p>
                        @endif
                    @else
                        <p>  {{ __('messages.No documents found') }}  </p>
                    @endif
                    </ul>
                </div>
<!--                 
                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Upload New SCAC Documents') }} </label>
                    <input type="file" name="scac_documents[]" class="form-control" multiple>
                    <small class="text-muted">  {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('scac_documents')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    @foreach ($errors->get('scac_documents.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div> -->
                <div class="mb-3">
    <label class="form-label">{{ __('messages.SCAC Legal Documents') }}</label>
    <div class="drop-zone" id="dropZone-scac">
        <i class="fas fa-upload"></i>
        <span id="dropZoneText-scac">{{ __('messages.Drag & drop or click to upload') }}</span>
        <input type="file" name="scac_documents[]" id="fileInput-scac" multiple>
    </div>
    <div class="file-names" id="fileNames-scac"></div>
    <small class="text-muted">{{ __('messages.You can upload multiple legal documents') }}</small>
    
    @error('scac_documents')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @foreach ($errors->get('scac_documents.*') as $message)
        <div class="text-danger">{{ $message[0] }}</div>
    @endforeach
</div>
                </div>
                <div class="upload-design">

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Uploaded CAAT Documents') }} </label>

                    @if($supplier->supplierdocuments && $decodedDocuments = json_decode($supplier->supplierdocuments, true))
                        @php
                            $filteredDocuments = collect($decodedDocuments)->filter(function($document) {
                                return isset($document['document_type']) && $document['document_type'] === 'caat_documents';
                            });
                        @endphp

                        @if($filteredDocuments->isNotEmpty())
                            <ul>
                                @foreach($filteredDocuments as $index => $document)
                                    <li>
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank">  {{ __('messages.View Document') }} </a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}">  {{ __('messages.Delete') }} 
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>  {{ __('messages.No documents found') }} </p>
                        @endif
                    @else
                        <p>  {{ __('messages.No documents found') }}  </p>
                    @endif
                </div>
                <!-- <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Upload New CAAT Documents') }} </label>
                    <input type="file" name="caat_documents[]" class="form-control" multiple>
                    <small class="text-muted">  {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('caat_documents')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    @foreach ($errors->get('caat_documents.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div> -->
                <div class="mb-3">
    <label class="form-label">{{ __('messages.CAAT Legal Documents') }}</label>
    <div class="drop-zone" id="dropZone-caat">
        <i class="fas fa-upload"></i>
        <span id="dropZoneText-caat">{{ __('messages.Drag & drop or click to upload') }}</span>
        <input type="file" name="caat_documents[]" id="fileInput-caat" multiple>
    </div>
    <div class="file-names" id="fileNames-caat"></div>
    <small class="text-muted">{{ __('messages.You can upload multiple legal documents') }}</small>
    
    @error('caat_documents')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @foreach ($errors->get('caat_documents.*') as $message)
        <div class="text-danger">{{ $message[0] }}</div>
    @endforeach
</div>
                </div>

                <div class="upload-design">

                <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Uploaded CTPAT Documents') }}</label>

                    @if($supplier->supplierdocuments && $decodedDocuments = json_decode($supplier->supplierdocuments, true))
                        @php
                            $filteredDocuments = collect($decodedDocuments)->filter(function($document) {
                                return isset($document['document_type']) && $document['document_type'] === 'ctpat_documents';
                            });
                        @endphp

                        @if($filteredDocuments->isNotEmpty())
                            <ul>
                                @foreach($filteredDocuments as $index => $document)
                                    <li>
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank">  {{ __('messages.View Document') }} </a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}">  {{ __('messages.Delete') }} 
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>  {{ __('messages.No documents found') }} </p>
                        @endif
                    @else
                        <p>  {{ __('messages.No documents found') }}  </p>
                    @endif
                </div>
                <!-- <div class="mb-3">
                    <label class="form-label">  {{ __('messages.Upload New CTPAT Documents') }} </label>
                    <input type="file" name="ctpat_documents[]" class="form-control" multiple>
                    <small class="text-muted">  {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('ctpat_documents')
                        <div class="text-danger">
                        {{ $message }}
                        </div>
                    @enderror

                    @foreach ($errors->get('ctpat_documents.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div> -->
                <div class="mb-3">
    <label class="form-label">{{ __('messages.CTPAT Legal Documents') }}</label>
    <div class="drop-zone" id="dropZone-ctpat">
        <i class="fas fa-upload"></i>
        <span id="dropZoneText-ctpat">{{ __('messages.Drag & drop or click to upload') }}</span>
        <input type="file" name="ctpat_documents[]" id="fileInput-ctpat" multiple>
    </div>
    <div class="file-names" id="fileNames-ctpat"></div>
    <small class="text-muted">{{ __('messages.You can upload multiple legal documents') }}</small>
    
    @error('ctpat_documents')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @foreach ($errors->get('ctpat_documents.*') as $message)
        <div class="text-danger">{{ $message[0] }}</div>
    @endforeach
</div>
                </div>
                    <button type="submit" class="btn btn-primary btn_primary_color">  {{ __('messages.Update Supplier') }} </button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js_scripts')

<script>
    // Enable Select2 for multi-select dropdowns (Optional)
    $('#service_type').select2({ width: '100%' });

    const fileFields = ['document', 'scac', 'caat', 'ctpat'];

fileFields.forEach(field => {
    const dropZone = document.getElementById(`dropZone-${field}`);
    const fileInput = document.getElementById(`fileInput-${field}`);
    const fileNames = document.getElementById(`fileNames-${field}`);
    const dropZoneText = document.getElementById(`dropZoneText-${field}`);

    // Click to trigger input
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag over styling
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    // Remove styling on leave
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    // Handle dropped files
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileNames(e.dataTransfer.files, fileNames, dropZoneText);
        }
    });

    // Update when choosing files manually
    fileInput.addEventListener('change', () => {
        updateFileNames(fileInput.files, fileNames, dropZoneText);
    });
});

function updateFileNames(files, container, textElement) {
    if (files.length) {
        let names = [];
        for (let i = 0; i < files.length; i++) {
            names.push(`<div>📎 ${files[i].name}</div>`);
        }
        container.innerHTML = names.join('');
        textElement.textContent = "{{ __('messages.Files Selected:') }}";
    } else {
        container.innerHTML = '';
        textElement.textContent = "{{ __('messages.Drag & drop or click to upload') }}";
    }
}
</script>

@endsection