@extends('layout.app')

@section('title', 'Supplier Details')
{{-- @section('sub-title', 'Supplier') --}}
@section('sub-title', __('messages.Supplier'))


@section('content')
<div class="main_cont_outer">
<div class="create_btn mb-3">
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"> </i> {{ __('messages.Back') }}
        </a>
    </div>
<div class="container mt-4">

    <!-- Supplier Information -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">  {{ __('messages.Company Details') }} </h5>
            <p class="card-text">
                <strong>  {{ __('messages.Company Name') }} :</strong> {{ $supplier->company_name }} <br>
                <strong>  {{ __('messages.DBA') }} :</strong> {{ $supplier->dba }} <br>
                <strong>  {{ __('messages.Address') }} :</strong> {{ $supplier->street_address }}, {{ $supplier->city }}, {{ $supplier->state }} - {{ $supplier->zip_code }} <br>
                <strong>  {{ __('messages.Country') }} :</strong> {{ $supplier->country }} <br>
                <strong>  {{ __('messages.Office Phone') }} :</strong> {{ $supplier->office_phone }} <br>
                <strong>  {{ __('messages.Email') }} :</strong> {{ $supplier->primary_contact_email }} <br>
                <strong>  {{ __('messages.Primary Contact Office Phone') }} :</strong> {{ $supplier->primary_contact_office_phone }} <br>
                <strong>  {{ __('messages.Primary Contact Mobile Phone') }} :</strong> {{ $supplier->primary_contact_mobile_phone }} <br>
                <strong>  {{ __('messages.SCAC Number') }} :</strong> {{ $supplier->scac_number }} <br>
                <strong>  {{ __('messages.CAAT Number') }} :</strong> {{ $supplier->caat_number }} <br>
                <strong>  {{ __('messages.CTPAT Number') }} :</strong> {{ $supplier->ctpat_number }} <br>
            </p>
        </div>
    </div>

    <!-- User Details Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">  {{ __('messages.User Details') }} </h5>
            <p class="card-text">
                <strong>  {{ __('messages.User Role') }} :</strong> {{ str_replace('_', ' ', $supplier->user_role) }} <br>
                <strong>  {{ __('messages.User Email') }} :</strong> {{ $supplier->user_email }} <br>
                <strong>  {{ __('messages.User Office Phone') }} :</strong> {{ $supplier->user_office_phone }} <br>
                <strong>  {{ __('messages.User Mobile Phone') }} :</strong> {{ $supplier->user_mobile_phone }} <br>
                <strong>  {{ __('messages.Service Type') }} :</strong> {{ $supplier->service_type }} <br>
                <strong>  {{ __('messages.Currency') }} :</strong> {{ $supplier->currency }} <br>
                <strong>  {{ __('messages.Preferred Language') }} :</strong> {{ $supplier->preferred_language }} <br>
            </p>
        </div>
    </div>


    <!-- Documents Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">  {{ __('messages.Uploaded Documents') }} </h5>

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
                                    <i class="fa fa-file-pdf-o"></i>  {{ __('messages.View Document') }} 
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p> {{ __('messages.No valid documents found') }} </p>
                @endif
            @else   
                <p> {{ __('messages.No documents uploaded') }} </p>
            @endif

            <hr>

            <h5 class="card-title">  {{ __('messages.SCAC Documents') }} </h5>
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
                                    <i class="fa fa-file-pdf-o"></i>  {{ __('messages.View Document') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>  {{ __('messages.No valid documents found') }}</p>
                @endif
            @else   
                <p>  {{ __('messages.No documents uploaded') }} </p>
            @endif

            <hr>

            <h5 class="card-title">  {{ __('messages.CAAT Documents') }} </h5>
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
                                        <i class="fa fa-file-pdf-o"></i>  {{ __('messages.View Document') }} 
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>  {{ __('messages.No valid documents found') }} </p>
                    @endif
                @else   
                    <p>  {{ __('messages.No documents uploaded') }} </p>
                @endif
        </div>
    </div>

    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color">  {{ __('messages.Back to Supplier List') }} </a>
</div>
</div>

@endsection