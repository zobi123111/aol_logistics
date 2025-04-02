@section('title', 'Loads')
@section('sub-title', __('messages.Loads'))
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
    <!-- <h2>QuickBooks Invoices for Load #</h2>
      -->
      <h2>QuickBooks Bill Details</h2>

@if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
@elseif(isset($quickBooksBill))
    <h3>Bill Information</h3>
    <table class="table table-bordered">
        <tr>
            <th>Bill ID</th>
            <td>{{ $quickBooksBill->Id }}</td>
        </tr>
        <tr>
            <th>Vendor ID</th>
            <td>{{ $quickBooksBill->VendorRef ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>${{ number_format($quickBooksBill->TotalAmt, 2) }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ \Carbon\Carbon::parse($quickBooksBill->TxnDate)->format('M d, Y') }}</td>
        </tr>
    </table>
    <h3>Line Items</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
                <th>Unit Price</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quickBooksBill->Line as $line)
                @if(isset($line->ItemBasedExpenseLineDetail))
                    <tr>
                        <td>{{ $line->Description ?? 'N/A' }}</td>
                        <td>${{ number_format($line->Amount, 2) }}</td>
                        <td>${{ number_format($line->ItemBasedExpenseLineDetail->UnitPrice ?? 0, 2) }}</td>
                        <td>{{ $line->ItemBasedExpenseLineDetail->Qty ?? 0 }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
       <div>
        <h3>Attachments</h3>
        @if (count($attachments) > 0)
            <ul>
                @foreach ($attachments as $attachment)
                    <li>
                        <a href="{{ $attachment->TempDownloadUri }}" target="_blank">
                            {{ $attachment->FileName }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No attachments available for this bill.</p>
        @endif
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