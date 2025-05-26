@section('title', 'Invoice')
@section('sub-title','Invoice')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
       <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }}</a>
    </div>
    <h3 class="services-text">{{ __('messages.Assigned Services') }} </h3> 
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif
     <div class="card card-container">
        <div class="card-body">
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
                    <td>{{ $assigned->service->masterService->service_type }}</td>
                    <td>{{ $assigned->service->masterService->service_name?? 'NA' }}</td>
                    <td>{{ $assigned->quantity }}</td>
                    <td>
                    @if ($assigned->service->masterService->service_type === 'warehouse')
                      
                    {{$assigned->service->masterService->street . ', ' . $assigned->service->masterService->city . ', ' . $assigned->service->masterService->state . ', ' . $assigned->service->masterService->zip . ', ' . $assigned->service->masterService->country}}

                        @else
                        {{ $assigned->service->masterService->origindata 
                        ? ($assigned->service->masterService->origindata->name 
                            ?: ($assigned->service->masterService->origindata->street . ', ' . $assigned->service->masterService->origindata->city . ', ' . $assigned->service->masterService->origindata->state . ', ' . $assigned->service->masterService->origindata->zip . ', ' . $assigned->service->masterService->origindata->country)) 
                        : 'N/A' }}  
                    →  
                    {{ $assigned->service->masterService->destinationdata 
                        ? ($assigned->service->masterService->destinationdata->name 
                            ?: ($assigned->service->masterService->destinationdata->street . ', ' . $assigned->service->masterService->destinationdata->city . ', ' . $assigned->service->masterService->destinationdata->state . ', ' . $assigned->service->masterService->destinationdata->zip . ', ' . $assigned->service->masterService->destinationdata->country)) 
                        : 'N/A' }}
                        @endif
                    </td>
                    <td>
                        ${{ number_format(($assigned->supplier_cost ?? $assigned->service->masterService->supplier_cost) * $assigned->quantity, 2) }}  
                        @if($assigned->quantity > 1)
                            <br>
                            <small class="text-muted">(${{ number_format($assigned->supplier_cost ?? $assigned->supplier_cost, 2) }} per unit)</small>
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
            <label for="bill_no" class="form-label">{{ __('messages.bill_no') }}</label>
            <input type="text" name="bill_no" class="form-control" value="{{ old('bill_no') }}">
            @error('bill_no')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="bill_pdf" class="form-label">{{ __('messages.upload_bill_pdf') }}</label>
            <input type="file" name="bill_pdf" class="form-control" >
            @error('bill_pdf')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn_primary_color">{{ __('messages.upload_bill') }}</button>
    </form>
</div>
</div>
</div>
@endsection