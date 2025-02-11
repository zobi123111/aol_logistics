@extends('layout.app')

@section('title', 'Supplier Details')
@section('sub-title', 'Supplier')

@section('content')
<div class="container mt-4">

    <!-- Supplier Information -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Company Details</h5>
            <p class="card-text">
                <strong>Company Name:</strong> {{ $supplier->company_name }} <br>
                <strong>DBA:</strong> {{ $supplier->dba }} <br>
                <strong>Address:</strong> {{ $supplier->street_address }}, {{ $supplier->city }}, {{ $supplier->state }}
                - {{ $supplier->zip_code }} <br>
                <strong>Country:</strong> {{ $supplier->country }} <br>
                <strong>Office Phone:</strong> {{ $supplier->office_phone }} <br>
                <strong>Email:</strong> {{ $supplier->primary_contact_email }} <br>
                <strong>Primary Contact Office Phone:</strong> {{ $supplier->primary_contact_office_phone }} <br>
                <strong>Primary Contact Mobile Phone:</strong> {{ $supplier->primary_contact_mobile_phone }} <br>
                <strong>SCAC Number:</strong> {{ $supplier->scac_number }} <br>
                <strong>CAAT Number:</strong> {{ $supplier->caat_number }} <br>
            </p>
        </div>
    </div>

    <!-- User Details Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">User Details</h5>
            <p class="card-text">
                <strong>User Role:</strong> {{ $supplier->user_role }} <br>
                <strong>User Email:</strong> {{ $supplier->user_email }} <br>
                <strong>User Office Phone:</strong> {{ $supplier->user_office_phone }} <br>
                <strong>User Mobile Phone:</strong> {{ $supplier->user_mobile_phone }} <br>
                <strong>Service Type:</strong> {{ $supplier->service_type }} <br>
                <strong>Currency:</strong> {{ $supplier->currency }} <br>
                <strong>Preferred Language:</strong> {{ $supplier->preferred_language }} <br>
            </p>
        </div>
    </div>


    <!-- Documents Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Uploaded Documents</h5>

            @if($supplier->documents && json_decode($supplier->documents))
            <ul class="list-unstyled document-lists">
                @foreach(json_decode($supplier->documents) as $document)
                <li>
                    @if (in_array(pathinfo($document, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                    <!-- Show image for document if it's an image file -->
                    <img src="{{ asset('storage/' . $document) }}" alt="{{ basename($document) }}"
                        class="img-thumbnail mt-2" style="max-width: 150px; max-height: 150px;">
                    @endif
                    <a href="{{ asset('storage/' . $document) }}" target="_blank" class="text-decoration-none">
                        <i class="fa fa-file-pdf-o"></i> View Document
                    </a>
                </li>
                @endforeach
            </ul>
            @else
            <p>No documents uploaded.</p>
            @endif

            <hr>

            <h5 class="card-title">SCAC Documents</h5>
            @if($supplier->scac_documents && json_decode($supplier->scac_documents))
            <ul class="list-unstyled document-lists">
                @foreach(json_decode($supplier->scac_documents) as $document)
                <li>
                    @if (in_array(pathinfo($document, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $document) }}" alt="{{ basename($document) }}"
                        class="img-thumbnail mt-2" style="max-width: 150px; max-height: 150px;">
                    @endif
                    <a href="{{ asset('storage/' . $document) }}" target="_blank" class="text-decoration-none">
                        <i class="fa fa-file-pdf-o"></i> View Document
                    </a>
                </li>
                @endforeach
            </ul>
            @else
            <p>No SCAC documents uploaded.</p>
            @endif

            <hr>

            <h5 class="card-title">CAAT Documents</h5>
            @if($supplier->caat_documents && json_decode($supplier->caat_documents))
            <ul class="list-unstyled document-lists">
                @foreach(json_decode($supplier->caat_documents) as $document)
                <li>
                    @if (in_array(pathinfo($document, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $document) }}" alt="{{ basename($document) }}"
                        class="img-thumbnail mt-2" style="max-width: 150px; max-height: 150px;">
                    @endif
                    <a href="{{ asset('storage/' . $document) }}" target="_blank" class="text-decoration-none">
                        <i class="fa fa-file-pdf-o"></i> View Document
                    </a>
                </li>
                @endforeach
            </ul>
            @else
            <p>No CAAT documents uploaded.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color">Back to Supplier
        List</a>
</div>
@endsection