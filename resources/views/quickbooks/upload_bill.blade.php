@section('title', 'Invoice')
@section('sub-title','Invoice')
@extends('layout.app')
@section('content')
<div class="container">
    <h3 class="services-text">{{ __('messages.Assigned Services') }} </h3> 
<table class="table" id="assignedServices">
    <thead>
        <tr>
            <!-- <th>{{ __('messages.Supplier Company Name') }} </th> -->
            <th>{{ __('messages.supplier_transport_type') }}</th>
            <th>{{ __('messages.service_type') }}</th>
             <th> {{ __('messages.Service Name') }}  </th>
            <th>{{ __('messages.quantity') }}</th>
            <th>{{ __('messages.Service Details') }} </th>
            <th>{{ __('messages.Cost') }} </th>
        </tr>
    </thead>
    <tbody>

        <!-- Show Assigned Services at the Top -->
        @if($assignedServices->isEmpty())
            <tr>
                <td colspan="6" class="text-center">{{ __('messages.No Assigned Services') }} </td>
            </tr>
        @else
            @foreach ($assignedServices as $assigned)
                <tr>
                    <!-- <td>{{ $assigned->supplier->company_name }}</td> -->
                    <td>{{ $assigned->supplier->service_type }}</td>
                    <td>{{ $assigned->service->service_type }}</td>
                    <td>{{ $assigned->service->service_name?? 'NA' }}</td>
                    <td>{{ $assigned->quantity }}</td>
                    <td>
                    @if ($assigned->service->service_type === 'warehouse')
                      
                    {{$assigned->service->street . ', ' . $assigned->service->city . ', ' . $assigned->service->state . ', ' . $assigned->service->zip . ', ' . $assigned->service->country}}

                        @else
                        {{ $assigned->service->origindata 
                        ? ($assigned->service->origindata->name 
                            ?: ($assigned->service->origindata->street . ', ' . $assigned->service->origindata->city . ', ' . $assigned->service->origindata->state . ', ' . $assigned->service->origindata->zip . ', ' . $assigned->service->origindata->country)) 
                        : 'N/A' }}  
                    →  
                    {{ $assigned->service->destinationdata 
                        ? ($assigned->service->destinationdata->name 
                            ?: ($assigned->service->destinationdata->street . ', ' . $assigned->service->destinationdata->city . ', ' . $assigned->service->destinationdata->state . ', ' . $assigned->service->destinationdata->zip . ', ' . $assigned->service->destinationdata->country)) 
                        : 'N/A' }}
                        @endif
                    </td>
                    <td>
                        ${{ number_format(($assigned->cost ?? $assigned->service->cost) * $assigned->quantity, 2) }}  
                        @if($assigned->quantity > 1)
                            <br>
                            <small class="text-muted">(${{ number_format($assigned->cost ?? $assigned->service->cost, 2) }} per unit)</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<form action="{{ route('upload.bill', ['load_id' => $load_id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="mb-3">
            <label for="bill_pdf" class="form-label">Upload Bill PDF</label>
            <input type="file" name="bill_pdf" class="form-control" >
            @error('bill_pdf')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Upload Bill</button>
    </form>
</div>
@endsection