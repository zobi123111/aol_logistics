@extends('layout.app')

@section('title', 'Edit Supplier')
{{-- @section('sub-title', 'Edit Supplier') --}}
@section('sub-title', GoogleTranslate::trans('Edit Supplier', app()->getLocale()))


@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color">
            <i class="bi bi-arrow-left-circle-fill"></i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }}
        </a>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}
    </div>
    @endif

    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Company Name -->
                <div class="mb-3 mt-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Full and Legal Name of the Company', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $supplier->company_name) }}">
                    @error('company_name')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <!-- DBA -->
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('DBA (Nickname)', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="dba" class="form-control" value="{{ old('dba', $supplier->dba) }}">
                    @error('dba')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <!-- Address Fields -->
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Street Address', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="street_address" class="form-control" value="{{ old('street_address', $supplier->street_address) }}">
                    @error('street_address')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ GoogleTranslate::trans('City', app()->getLocale()) }} </label><span class="text-danger">*</span>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city) }}">
                        @error('city')
                            <div class="text-danger">
                                {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                            </div>
                    @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ GoogleTranslate::trans('State', app()->getLocale()) }} </label><span class="text-danger">*</span>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $supplier->state) }}">
                        @error('state')
                            <div class="text-danger">
                                {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ GoogleTranslate::trans('Zip Code', app()->getLocale()) }} </label><span class="text-danger">*</span>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $supplier->zip_code) }}">
                        @error('zip_code')
                            <div class="text-danger">
                                {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Country', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <select name="country" class="form-select">
                        <option value="USA" {{ old('country', $supplier->country) == 'USA' ? 'selected' : '' }}>United States</option>
                        <option value="Mexico" {{ old('country', $supplier->country) == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                        <option value="Canada" {{ old('country', $supplier->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                        <option value="Other" {{ old('country', $supplier->country) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('country')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <!-- Contact Details -->
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Office Phone', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="office_phone" class="form-control" value="{{ old('office_phone', $supplier->office_phone) }}">
                    @error('office_phone')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Primary Contact Email', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $supplier->primary_contact_email) }}">
                    @error('primary_contact_email')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Primary Contact Office Phone', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_office_phone" class="form-control" value="{{ old('primary_contact_office_phone', $supplier->primary_contact_office_phone) }}">
                    @error('primary_contact_office_phone')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Primary Contact Mobile Phone', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_mobile_phone" class="form-control" value="{{ old('primary_contact_mobile_phone', $supplier->primary_contact_mobile_phone) }}">
                    @error('primary_contact_mobile_phone')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('User Email', app()->getLocale()) }}  <small> {{ GoogleTranslate::trans('(Used for login)', app()->getLocale()) }} </small></label><span class="text-danger">*</span>
                    <input type="email" name="user_email" class="form-control" value="{{ old('user_email', $supplier->user_email) }}">
                    @error('user_email')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('User Office Phone', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_office_phone" class="form-control" value="{{ old('user_office_phone', $supplier->user_office_phone) }}">
                    @error('user_office_phone')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('User Mobile Phone', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_mobile_phone" class="form-control" value="{{ old('user_mobile_phone', $supplier->user_mobile_phone) }}">
                    @error('user_mobile_phone')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Password', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                    @error('password')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

             
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Confirm Password', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="password" name="password_confirmation" class="form-control"
                        value="{{ old('password_confirmation') }}">
                    @error('password_confirmation')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Service Type', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <select name="service_type" class="form-select">
                        <option value="Land Freight" {{ old('service_type', $supplier->service_type) == 'Land Freight' ? 'selected' : '' }}>Land Freight</option>
                        <option value="Air Freight" {{ old('service_type', $supplier->service_type) == 'Air Freight' ? 'selected' : '' }}>Air Freight</option>
                        <option value="Ocean Freight" {{ old('service_type', $supplier->service_type) == 'Ocean Freight' ? 'selected' : '' }}>Ocean Freight</option>
                    </select>
                    @error('service_type')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Will the supplier invoice in', app()->getLocale()) }} </label><span class="text-danger">*</span>
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
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

            
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Preferred Language', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <select name="preferred_language" class="form-select">
                        <option value="english" {{ old('preferred_language', $supplier->preferred_language) == 'english' ? 'selected' : '' }}>English</option>
                        <option value="spanish" {{ old('preferred_language', $supplier->preferred_language) == 'spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="french" {{ old('preferred_language', $supplier->preferred_language) == 'french' ? 'selected' : '' }}>French</option>
                        <option value="german" {{ old('preferred_language', $supplier->preferred_language) == 'german' ? 'selected' : '' }}>German</option>
                    </select>
                    @error('preferred_language')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('SCAC Number', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="scac_number" class="form-control" value="{{ old('scac_number', $supplier->scac_number) }}">
                    @error('scac_number')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

              

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('CAAT Number', app()->getLocale()) }} </label><span class="text-danger">*</span>
                    <input type="text" name="caat_number" class="form-control" value="{{ old('caat_number', $supplier->caat_number) }}">
                    @error('caat_number')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Uploaded Documents', app()->getLocale()) }} </label>
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
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank"> {{ GoogleTranslate::trans('View Document', app()->getLocale()) }} </a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}"> {{ GoogleTranslate::trans('Delete', app()->getLocale()) }}  
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p> {{ GoogleTranslate::trans('No documents found with type documents', app()->getLocale()) }} </p>
                        @endif
                    @else
                        <p> {{ GoogleTranslate::trans('No documents available', app()->getLocale()) }} </p>
                    @endif
                    </ul>
                </div>
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Upload New Documents', app()->getLocale()) }} </label>
                    <input type="file" name="document_path[]" class="form-control" multiple>
                    <small class="text-muted">  {{ GoogleTranslate::trans('You can upload multiple documents', app()->getLocale()) }} </small>
                    <!-- Loop to show any validation errors for the document_path array -->
                    @error('document_path')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('document_path.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>

                <!-- SCAC Documents -->
          
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Uploaded SCAC Documents', app()->getLocale()) }} </label>

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
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank">{{ GoogleTranslate::trans('View Document', app()->getLocale()) }}</a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}"> {{ GoogleTranslate::trans('Delete', app()->getLocale()) }} Delete
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p> {{ GoogleTranslate::trans('No documents found', app()->getLocale()) }} </p>
                        @endif
                    @else
                        <p> {{ GoogleTranslate::trans('No documents available', app()->getLocale()) }} </p>
                    @endif
                    </ul>
                </div>
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Upload New SCAC Documents', app()->getLocale()) }} </label>
                    <input type="file" name="scac_documents[]" class="form-control" multiple>
                    <small class="text-muted"> {{ GoogleTranslate::trans('You can upload multiple legal documents', app()->getLocale()) }} </small>
                    @error('scac_documents')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('scac_documents.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Uploaded CAAT Documents', app()->getLocale()) }} </label>

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
                                        <a href="{{ asset('storage/' . $document['file_path']) }}" target="_blank"> {{ GoogleTranslate::trans('View Document', app()->getLocale()) }} </a>
                                        <label>
                                        <input type="checkbox" name="delete_documents[]" value="{{ $document['file_path'] }}"> {{ GoogleTranslate::trans('Delete', app()->getLocale()) }} 
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p> {{ GoogleTranslate::trans('No documents found', app()->getLocale()) }} </p>
                        @endif
                    @else
                        <p> {{ GoogleTranslate::trans('No documents available', app()->getLocale()) }} </p>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label"> {{ GoogleTranslate::trans('Upload New CAAT Documents', app()->getLocale()) }} </label>
                    <input type="file" name="caat_documents[]" class="form-control" multiple>
                    <small class="text-muted"> {{ GoogleTranslate::trans('You can upload multiple legal documents', app()->getLocale()) }} </small>
                    @error('caat_documents')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('caat_documents.*') as $message)
                    <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>
                    <button type="submit" class="btn btn-primary btn_primary_color"> {{ GoogleTranslate::trans('Update Supplier', app()->getLocale()) }} </button>
            </form>
        </div>
    </div>
</div>
@endsection
