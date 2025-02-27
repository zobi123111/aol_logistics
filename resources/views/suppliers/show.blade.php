@extends('layout.app')

@section('title', 'Supplier Details')
{{-- @section('sub-title', 'Supplier') --}}
@section('sub-title', GoogleTranslate::trans('Edit Supplier', app()->getLocale()))

@section('content')
<div class="main_cont_outer">
<div class="create_btn mb-3">
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }}
        </a>
    </div>
<div class="container mt-4">

    <!-- Supplier Information -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"> {{ GoogleTranslate::trans('Company Details', app()->getLocale()) }} </h5>
            <p class="card-text">
                <strong> {{ GoogleTranslate::trans('Company Name', app()->getLocale()) }} :</strong> {{ $supplier->company_name }} <br>
                <strong> {{ GoogleTranslate::trans('DBA', app()->getLocale()) }} :</strong> {{ $supplier->dba }} <br>
                <strong> {{ GoogleTranslate::trans('Address', app()->getLocale()) }} :</strong> {{ $supplier->street_address }}, {{ $supplier->city }}, {{ $supplier->state }} - {{ $supplier->zip_code }} <br>
                <strong> {{ GoogleTranslate::trans('Country', app()->getLocale()) }} :</strong> {{ $supplier->country }} <br>
                <strong> {{ GoogleTranslate::trans('Office Phone', app()->getLocale()) }} :</strong> {{ $supplier->office_phone }} <br>
                <strong> {{ GoogleTranslate::trans('Email', app()->getLocale()) }} :</strong> {{ $supplier->primary_contact_email }} <br>
                <strong> {{ GoogleTranslate::trans('Primary Contact Office Phone', app()->getLocale()) }} :</strong> {{ $supplier->primary_contact_office_phone }} <br>
                <strong> {{ GoogleTranslate::trans('Primary Contact Mobile Phone', app()->getLocale()) }} :</strong> {{ $supplier->primary_contact_mobile_phone }} <br>
                <strong> {{ GoogleTranslate::trans('SCAC Number', app()->getLocale()) }} :</strong> {{ $supplier->scac_number }} <br>
                <strong> {{ GoogleTranslate::trans('CAAT Number', app()->getLocale()) }} :</strong> {{ $supplier->caat_number }} <br>
            </p>
        </div>
    </div>

    <!-- User Details Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"> {{ GoogleTranslate::trans('User Details', app()->getLocale()) }} </h5>
            <p class="card-text">
                <strong> {{ GoogleTranslate::trans('User Role', app()->getLocale()) }} :</strong> {{ str_replace('_', ' ', $supplier->user_role) }} <br>
                <strong> {{ GoogleTranslate::trans('User Email', app()->getLocale()) }} :</strong> {{ $supplier->user_email }} <br>
                <strong> {{ GoogleTranslate::trans('User Office Phone', app()->getLocale()) }} :</strong> {{ $supplier->user_office_phone }} <br>
                <strong> {{ GoogleTranslate::trans('User Mobile Phone', app()->getLocale()) }} :</strong> {{ $supplier->user_mobile_phone }} <br>
                <strong> {{ GoogleTranslate::trans('Service Type', app()->getLocale()) }} :</strong> {{ $supplier->service_type }} <br>
                <strong> {{ GoogleTranslate::trans('Currency', app()->getLocale()) }} :</strong> {{ $supplier->currency }} <br>
                <strong> {{ GoogleTranslate::trans('Preferred Language', app()->getLocale()) }} :</strong> {{ $supplier->preferred_language }} <br>
            </p>
        </div>
    </div>


    <!-- Documents Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"> {{ GoogleTranslate::trans('Uploaded Documents', app()->getLocale()) }} </h5>

            @if($supplier->supplierdocuments && json_decode($supplier->supplierdocuments, true))
                @php
                    $documents = array_filter(json_decode($supplier->supplierdocuments, true), function ($document) {
                        return isset($document['document_type']) && $document['document_type'] === 'documents';
                    });
                @endphp

                @if (!empty($documents))
                    <ul class="list-unstyled document-lists">
                        @foreach($documents as $document)
                            <li>
                                @if (in_array(pathinfo($document['file_path'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                    <img src="{{ asset('storage/' . $document['file_path']) }}" 
                                        alt="{{ basename($document['file_path']) }}"
                                        class="img-thumbnail mt-2" 
                                        style="max-width: 150px; max-height: 150px;">
                                @endif
                                <a href="{{ asset('storage/' . $document['file_path']) }}" 
                                    target="_blank" class="text-decoration-none">
                                    <i class="fa fa-file-pdf-o"></i> {{ GoogleTranslate::trans('DView DocumentBA', app()->getLocale()) }} 
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>{{ GoogleTranslate::trans('No valid documents found', app()->getLocale()) }} </p>
                @endif
            @else   
                <p>{{ GoogleTranslate::trans('No documents uploaded', app()->getLocale()) }} </p>
            @endif

            <hr>

            <h5 class="card-title"> {{ GoogleTranslate::trans('SCAC Documents', app()->getLocale()) }} </h5>
            @if($supplier->supplierdocuments && json_decode($supplier->supplierdocuments, true))
                @php
                    $documents = array_filter(json_decode($supplier->supplierdocuments, true), function ($document) {
                        return isset($document['document_type']) && $document['document_type'] === 'scac_documents';
                    });
                @endphp

                @if (!empty($documents))
                    <ul class="list-unstyled document-lists">
                        @foreach($documents as $document)
                            <li>
                                @if (in_array(pathinfo($document['file_path'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                    <img src="{{ asset('storage/' . $document['file_path']) }}" 
                                        alt="{{ basename($document['file_path']) }}"
                                        class="img-thumbnail mt-2" 
                                        style="max-width: 150px; max-height: 150px;">
                                @endif
                                <a href="{{ asset('storage/' . $document['file_path']) }}" 
                                    target="_blank" class="text-decoration-none">
                                    <i class="fa fa-file-pdf-o"></i> {{ GoogleTranslate::trans('View Document', app()->getLocale()) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p> {{ GoogleTranslate::trans('No valid documents found', app()->getLocale()) }}</p>
                @endif
            @else   
                <p> {{ GoogleTranslate::trans('No documents uploaded', app()->getLocale()) }} </p>
            @endif

            <hr>

            <h5 class="card-title"> {{ GoogleTranslate::trans('CAAT Documents', app()->getLocale()) }} </h5>
                @if($supplier->supplierdocuments && json_decode($supplier->supplierdocuments, true))
                    @php
                        $documents = array_filter(json_decode($supplier->supplierdocuments, true), function ($document) {
                            return isset($document['document_type']) && $document['document_type'] === 'caat_documents';
                        });
                    @endphp

                    @if (!empty($documents))
                        <ul class="list-unstyled document-lists">
                            @foreach($documents as $document)
                                <li>
                                    @if (in_array(pathinfo($document['file_path'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset('storage/' . $document['file_path']) }}" 
                                            alt="{{ basename($document['file_path']) }}"
                                            class="img-thumbnail mt-2" 
                                            style="max-width: 150px; max-height: 150px;">
                                    @endif
                                    <a href="{{ asset('storage/' . $document['file_path']) }}" 
                                        target="_blank" class="text-decoration-none">
                                        <i class="fa fa-file-pdf-o"></i> {{ GoogleTranslate::trans('View Document', app()->getLocale()) }} 
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p> {{ GoogleTranslate::trans('No valid documents found', app()->getLocale()) }} </p>
                    @endif
                @else   
                    <p> {{ GoogleTranslate::trans('No documents uploaded', app()->getLocale()) }} </p>
                @endif
        </div>
    </div>

    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"> {{ GoogleTranslate::trans('Back to Supplier List', app()->getLocale()) }} </a>
</div>
</div>

@endsection