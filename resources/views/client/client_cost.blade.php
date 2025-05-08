@section('title', 'Client')
 <!-- @section('sub-title', 'Client') -->
@section('sub-title', __('messages.Client'). ' | Business: ' . $client->business_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('client.create') }}" class="btn btn-primary create-button btn_primary_color" id="createClient"> 
            Back
        </a>
        <a href="javascript:void(0);" class="btn btn-primary create-button btn_primary_color" id="save-costs-btn">Save Costs</a>
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
    <form id="client-cost-form" method="POST" action="{{ route('client_cost.save') }}">
        @csrf
        <input type="hidden" name="client_id" value="{{ $clientId }}">

        <table class="table table-bordered" id="client-cost-table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Service</th>
                    <th>Client Cost</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </form>
</div>
@endsection
@section('js_scripts')

<script>
$(document).ready(function () {
    let table = $('#client-cost-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("client_cost.index", $clientId) }}',
        columns: [
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'service_name', name: 'service_name' },
            { data: 'client_cost', name: 'client_cost', orderable: false, searchable: false },
        ],
        drawCallback: function () {
            // You can add additional JS logic here if needed
        }
    });

});
$('#save-costs-btn').on('click', function() {
    $('#client-cost-form').submit();  // Trigger the form submission
});

</script>

@endsection