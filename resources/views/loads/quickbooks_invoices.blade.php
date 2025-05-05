@section('title', __('messages.Services'))
@section('sub-title', __('messages.Services'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
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

    <div class="card card-container">
    <div class="card-body">
    <h2>QuickBooks Invoices for Load #{{ $load->aol_number }}</h2>

    @if(count($quickBooksInvoices) > 0)
        @foreach ($quickBooksInvoices as $invoiceData)
            <div class="card mb-4">
                <div class="card-header btn_primary_color">
                    <h4>Invoice #{{$invoiceData['invoice']->Id ?? 'N/A'}} DocNumber( {{$invoiceData['invoice']->DocNumber ?? 'N/A'}} )</h4>
                </div>
                <div class="card-body">
                    <h5 class="text-muted">Customer Details</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><strong>Name:</strong> {{ $invoiceData['customer']->DisplayName ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $invoiceData['customer']->PrimaryEmailAddr->Address ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Company:</strong> {{ $invoiceData['customer']->CompanyName ?? 'N/A' }}</li>
                    </ul>

                    <h5 class="text-muted">Invoice Details</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoiceData['invoice']->TxnDate)->format('M d, Y') ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Due Date:</strong> {{ isset($invoiceData['invoice']->DueDate) ? \Carbon\Carbon::parse($invoiceData['invoice']->DueDate)->format('M d, Y') : 'N/A' }}</li>
                        <li class="list-group-item"><strong>Total Amount:</strong> ${{ number_format($invoiceData['invoice']->TotalAmt ?? 0, 2) }}</li>
                        <li class="list-group-item"><strong>Status:</strong> {{ $invoiceData['invoice']->Balance > 0 ? 'Unpaid' : 'Paid' }}</li>
                    </ul>

                    <h5 class="text-muted">Services</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            // Filter services to exclude those with a zero amount
                            $filteredServices = array_filter($invoiceData['services'], function($service) {
                                return $service['rate'] > 0;
                            });
                        @endphp
                            @if(count($filteredServices) > 0)
                                @foreach ($filteredServices as $service)
                                    <tr>
                                        <td>{{ $service['description'] }}</td>
                                        <td>{{ $service['quantity'] }}</td>
                                        <td>${{ number_format($service['rate'], 2) }}</td>
                                        <td>${{ number_format($service['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No services found for this invoice.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            No QuickBooks invoices found for this load.
        </div>
    @endif
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