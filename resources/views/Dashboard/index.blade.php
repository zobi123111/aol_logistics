@section('title', 'Dashboard')
@extends('layout.app')
@section('content')
<section class="section dashboard">
    <div class="">
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
      {{ session()->get('message') }} 
    </div>
    @endif
        <!-- {{-- <p>Welcome to {{ env('PROJECT_NAME')}} dashboard </p> --}} -->
        <!-- <p>{{ __('messages.Welcome to') . ' ' . env('PROJECT_NAME') . ' ' . __('messages.Dashboard') }}</p> -->

    </div>
   

<div class="container">
    <!-- Welcome Section -->
    <div class="text-center py-4 mb-4 border-bottom">
        <!-- <h3 class="fw-bold">{{ __('messages.Welcome') }} {{ Auth::user()->fname }} {{ Auth::user()->lname }} !</h3> -->
        <h3 class="fw-bold">{{ __('messages.Welcome') }} {{ Auth::user()->fname }} !</h3>
        <!-- <p class="text-muted lead">{{ __('messages.Hey') }} <strong>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</strong>, {{ __("messages.we're excited to have you on board") }}! 😊</p> -->
    </div>

    <!-- User Profile Card -->
    <!-- <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    <h5 class="card-title">🔑 {{ __('messages.Your Account Details') }}</h5>
                    <ul class="list-unstyled mb-0">
                        <li>📧 <strong>{{ __('messages.Email') }}:</strong> {{ Auth::user()->email }}</li>
                        <li>🏷 <strong>{{ __('messages.User Type') }}:</strong> 
                            <span class="badge bg-primary">{{ Auth::user()->roledata->userType->name ?? 'Not Assigned' }}</span>
                        </li>
                        <li>🔰 <strong>{{ __('messages.Role') }}:</strong> 
                            <span class="badge bg-info">{{ Auth::user()->roledata->role_name ?? 'Not Assigned' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Active Users List -->
    <!-- <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    <h5 class="card-title">🚀 {{ __('messages.Active Users')}} <span class="badge bg-success" style="color:white">{{ $activeUsers->count() }}</span></h5>
                    <ul class="list-group mt-3">
                        @forelse($activeUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    👤 <strong>{{ $user->fname }} {{ $user->lname }}</strong> 
                                    <small class="text-muted">({{ $user->email }})</small> <br>
                                    🏷 <strong>{{ __('messages.User Type') }}:</strong> 
                                    <span class="badge bg-primary">{{ $user->roledata->userType->name ?? 'Not Assigned' }}</span> 
                                    🔰 <strong>{{ __('messages.Role') }}:</strong> 
                                    <span class="badge bg-info">{{ $user->roledata->role_name ?? 'Not Assigned' }}</span>
                                </div>
                                <span class="badge bg-success">🟢 {{ __('messages.Online') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-danger">😔 {{ __('messages.No active users found') }}.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    
                    <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#activeUsersList" aria-expanded="false" aria-controls="activeUsersList">
                        <h5 class="card-title"> {{ __('messages.Active Users') }} 
                            <span class="badge badge-success">{{ $activeUsers->count() }}</span>
                        </h5>                   
                     </button>
                    <div class="collapse mt-3" id="activeUsersList">
                        <ul class="list-group">
                            @forelse($activeUsers as $user)
                                <li class="list-group-item">
                                    <div>
                                        <strong>{{ $user->fname }} {{ $user->lname }}</strong> 
                                        <small class="text-muted">({{ $user->email }})</small><br>
                                        <strong>{{ __('messages.User Type') }}:</strong> {{ $user->roledata->userType->name ?? 'Not Assigned' }}<br>
                                        <strong>{{ __('messages.Role') }}:</strong> {{ $user->roledata->role_name ?? 'Not Assigned' }}
                                    </div>
                                    <span class="text-success">🟢 {{ __('messages.Online') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-danger">😔 {{ __('messages.No active users found') }}.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


     <!-- Active Users List -->
     <!-- <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                <h5 class="card-title">🚛 {{ __('messages.pending_loads') }} <span class="badge bg-success" style="color:white">{{ $pendingLoads->count() }}</span></h5>
                <table id="pendingLoadsTable" class="table table-striped respo_table" >
                        <thead>
                            <tr>
                                <th> {{ __('messages.AOL Number') }} </th>
                                <th> {{ __('messages.Origin') }} </th>
                                <th> {{ __('messages.Destination') }} </th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="row">

</div> -->


<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow-sm dashboard-design">
            <div class="card-body">
                <h5 class="card-title">🚛 🔍 {{ __('messages.filtered_loads') }}</h5>
                <div class="row mt-4">
                <div class="col-md-3">
                    <label for="supplierFilter">{{ __('messages.filter_by_supplier') }}</label>
                    <select id="supplierFilter" class="form-control" multiple>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplierdata->id }}">{{ $supplier->supplierdata->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="creatorFilter">{{ __('messages.filter_by_creator') }}</label>
                    <select id="creatorFilter" class="form-control" multiple>
                        @foreach($creators as $creator)
                            <option value="{{ $creator->creator->id }}">{{ isset($creator->creator->fname) ? $creator->creator->fname. ' '. $creator->creator->lname : $creator->creator->email }} 
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                <label for="client_filter">{{ __('messages.filter_by_client') }}</label>
                    <select id="client_filter" class="form-control select2" multiple>
                        @foreach($creatorsclients as $client)
                        <option value="{{ $client->creator->id }}">
                            {{ optional($client->creator->client)->business_name 
                                ?? $client->creator->business_name 
                                ?? $client->creator->fname . ' ' . $client->creator->lname }}
                        </option>
                        @endforeach
                    </select>
                </div>  

                <div class="col-md-3">
                    <label for="statusFilter">{{ __('messages.filter_by_status') }}</label>
                    <select id="statusFilter" class="form-control" multiple="multiple">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->status }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>


                </div>

                <div class="col-md-4">
                    <button id="applyFilters" class="btn btn-primary mt-4">{{ __('messages.apply_filters') }}</button>
                    <button id="clearFilters" class="btn btn-secondary mt-4">{{ __('messages.clear_filters') }}</button>
                </div>
                

                <h2 class="mt-4">{{ __('messages.pending_loads') }}</h2>
                <table id="pendingLoadsTablefilter" class="table table-striped respo_table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.aol_number') }}</th>
                            <th>{{ __('messages.origin') }}</th>
                            <th>{{ __('messages.Client') }}</th>
                            <th>{{ __('messages.destination') }}</th>
                            <th>{{ __('messages.supplier') }}</th>
                            <th>{{ __('messages.created_by') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">{{ __('messages.no_data') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- User Statistics Chart -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                <h5 class="card-title">📊 {{ __('messages.User Statistics (Active vs Total)') }}</h5>
                    <div id="userChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


</section>



@endsection

@section('js_scripts')

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
            chart: {
                type: "bar",
                height: 350
            },
            series: [
                {
                    name: "{{ __('messages.Active Users') }}",
                    className: "users_class",
                    data: [ {{ $activeTotalAol }}, {{ $activeSuppliers }} , {{ $activeClients }}]
                },
                {
                    name: "{{ __('messages.Total Users') }}",
                    className: "users_class",
                    data: [{{ $totalAol }}, {{ $totalSuppliers }}, {{ $totalClients }}]
                }
            ],
            xaxis: {
                categories: [
                    // "AOL Users", "Suppliers", "Clients"
                    "{{ __('messages.AOL Users') }}", 
                    "{{ __('messages.Suppliers') }}", 
                    "{{ __('messages.Client Users') }}"
                ]
            },
            colors: ["#28a745", "#007bff"], // Green for active, Blue for total
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "45%"
                }
            },
            dataLabels: {
                enabled: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#userChart"), options);
        chart.render();

        $('#pendingLoadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard', ['type' => 'loads']) }}",
                columns: [
                    { data: 'aol', name: 'aol_number' },
                    { data: 'originval', name: 'origin' },
                    { data: 'destinationval', name: 'destination' },
                ],
                language: {
                sSearch: "{{ __('messages.Search') }}",
                sLengthMenu: "{{ __('messages.Show') }} _MENU_ {{ __('messages.entries') }}",
                sInfo: "{{ __('messages.Showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
                oPaginate: {
                    sPrevious: "{{ __('messages.Previous') }}",
                    sNext: "{{ __('messages.Next') }}"
                }
            },
            });
     
    });

    function loadTable(suppliers = [], creators = [] , clients = [], statuses = []) {
        // Destroy previous instance if exists
        if ($.fn.DataTable.isDataTable("#pendingLoadsTablefilter")) {
            $("#pendingLoadsTablefilter").DataTable().destroy();
        }

        // Don't load table if no filters are applied
        if (suppliers.length === 0 && creators.length === 0 && clients.length === 0 && statuses.length === 0) {
            $("#pendingLoadsTablefilter tbody").html(
                `<tr><td colspan="7" class="text-center">{{ __('messages.no_data') }}</td></tr>`
            );
            return;
        }

        // Load DataTable only when filters are applied
        $("#pendingLoadsTablefilter").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('dashboard', ['type' => 'filter']) }}",
                data: {
                    supplier_ids: suppliers,
                    creator_ids: creators,
                    client_ids: clients,
                    status_ids: statuses
                }
            },
            columns: [
                { data: 'aol', name: 'aol_number' },
                { data: 'originval', name: 'origin' },
                { data: 'created_for_user', name: 'created_for_user', orderable: false, searchable: false },
                { data: 'destinationval', name: 'destination' },
                { data: 'supplier_name', name: 'supplierdata.company_name' },
                { data: 'creator_name', name: 'creator.fname' },
                { data: 'status', name: 'status' }
            ]
        });
    }

    // 🚀 **Don't load DataTable initially**
    $("#pendingLoadsTablefilter tbody").html(
        `<tr><td colspan="7" class="text-center">{{ __('messages.no_data') }}</td></tr>`
    );

    // Handle filter button click
    $('#applyFilters').click(function () {
        let selectedSuppliers = $('#supplierFilter').val() || [];
        let selectedCreators = $('#creatorFilter').val() || [];
        let selectedClient = $('#client_filter').val() || [];
        var statuses = $('#statusFilter').val(); 

        loadTable(selectedSuppliers, selectedCreators, selectedClient, statuses);
    });

    // Handle clear filters
    $('#clearFilters').click(function () {
        $('#supplierFilter').val([]).trigger('change');
        $('#creatorFilter').val([]).trigger('change');
        $('#client_filter').val([]).trigger('change');

        // Show message instead of table
        $("#pendingLoadsTablefilter tbody").html(
            `<tr><td colspan="7" class="text-center">{{ __('messages.no_data') }}</td></tr>`
        );
    });

    $('#statusFilter').select2({
        placeholder: 'Select status', // Placeholder text
        allowClear: true // Allow clearing the selection (optional)
    });

    // Enable Select2 for multi-select dropdowns (Optional)
    $('#supplierFilter, #creatorFilter, #client_filter',).select2({ width: '100%' });
</script>

@endsection