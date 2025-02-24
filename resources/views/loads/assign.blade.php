@section('title', 'Assign Load')
@section('sub-title', 'Assign Load')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> back</a>
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

        <table class="table mt-3" id="assign_loads">
    <thead>
        <tr>
            <th>Supplier Company Name</th>
            <th>Service Details</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @if($suppliers->isEmpty())
        <tr>
            <td colspan="4" class="text-center">No Service found</td>
        </tr>
        @else
        @foreach ($suppliers as $supplier)
            @foreach ($supplier->services as $service)
                <tr>
                    <td>{{ $supplier->company_name }}</td>
                    <td>
                        {{ $service->origin }} → {{ $service->destination }}
                    </td>
                    <td>${{ number_format($service->cost, 2) }}</td>
                    <td>
                    @if($load->supplier_id == $supplier->id)
                 <span class="badge bg-success">Assigned</span>
                @else
                    <a href="{{ route('loads.assign.supplier', ['load_id' => encode_id($load->id), 'supplier_id' => encode_id($supplier->id), 'service_id' => encode_id($service->id)]) }}" class="btn btn-primary">
                        Assign
                    </a>
                @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        @endif
    </tbody>
</table>

        </div>


@endsection

@section('js_scripts')

<script>
$(document).ready(function() {
    $('#assign_loads').DataTable()
});
</script>

@endsection